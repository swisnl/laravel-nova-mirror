<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\DeleteField;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Contracts\Deletable;
use Laravel\Nova\Http\Requests\DetachResourceRequest;

class ResourceDetachController extends Controller
{
    /**
     * Detach the given resource(s).
     *
     * @param  \Laravel\Nova\Http\Requests\DetachResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(DetachResourceRequest $request)
    {
        $request->chunks(150, function ($models) use ($request) {
            $parent = $request->findParentModelOrFail();

            foreach ($models as $model) {
                $this->deletePivotFields(
                    $request, $resource = $request->newResourceWith($model),
                    $pivot = $model->{$parent->{$request->viaRelationship}()->getPivotAccessor()}
                );

                $pivot->delete();

                DB::table('action_events')->insert(
                    ActionEvent::forResourceDetach(
                        $request->user(), $parent, collect([$model]),
                        $parent->{$request->viaRelationship}()->getPivotClass()
                    )->map->getAttributes()->all()
                );
            }
        });
    }

    /**
     * Delete the pivot fields on the given pivot model.
     *
     * @param  \Laravel\Nova\Http\Requests\DetachResourceRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Illuminate\Database\Eloquent\Model
     * @return void
     */
    protected function deletePivotFields(DetachResourceRequest $request, $resource, $pivot)
    {
        $resource->resolvePivotFields($request, $request->viaResource)
            ->whereInstanceOf(Deletable::class)
            ->filter->isPrunable()
            ->each(function ($field) use ($request, $pivot) {
                DeleteField::forRequest($request, $field, $pivot)->save();
            });
    }
}
