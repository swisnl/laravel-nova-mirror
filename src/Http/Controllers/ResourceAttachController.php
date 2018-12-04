<?php

namespace Laravel\Nova\Http\Controllers;

use DateTime;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResourceAttachController extends Controller
{
    /**
     * Attach a related resource to the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(NovaRequest $request)
    {
        $this->validate(
            $request, $model = $request->findModelOrFail(),
            $resource = $request->resource()
        );

        DB::transaction(function () use ($request, $resource, $model) {
            [$pivot, $callbacks] = $resource::fillPivot(
                $request, $model, $this->initializePivot(
                    $request, $model->{$request->viaRelationship}()
                )
            );

            $pivot->save();

            collect($callbacks)->each->__invoke();
        });
    }

    /**
     * Validate the attachment request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $resource
     * @return void
     */
    protected function validate(NovaRequest $request, $model, $resource)
    {
        $attribute = $resource::validationAttributeFor(
            $request, $request->relatedResource
        );

        Validator::make($request->all(), $resource::creationRulesFor(
            $request,
            $request->relatedResource
        ), [], [$request->relatedResource => $attribute])->validate();

        $resource::validateForAttachment($request);
    }

    /**
     * Initialize a fresh pivot model for the relationship.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Relations\BelongsToMany  $relationship
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    protected function initializePivot(NovaRequest $request, $relationship)
    {
        ($pivot = $relationship->newPivot())->forceFill([
            $relationship->getForeignPivotKeyName() => $request->resourceId,
            $relationship->getRelatedPivotKeyName() => $request->input($request->relatedResource),
        ]);

        if ($relationship->withTimestamps) {
            $pivot->forceFill([
                $relationship->createdAt() => new DateTime,
                $relationship->updatedAt() => new DateTime,
            ]);
        }

        return $pivot;
    }
}
