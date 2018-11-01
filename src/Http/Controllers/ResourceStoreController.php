<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class ResourceStoreController extends Controller
{
    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\CreateResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(CreateResourceRequest $request)
    {
        $resource = $request->resource();

        $resource::authorizeToCreate($request);

        $resource::validateForCreation($request);

        return DB::transaction(function () use ($request, $resource) {
            [$model, $callbacks] = $resource::fill(
                $request, $resource::newModel()
            );

            if ($request->viaRelationship()) {
                $request->findParentModelOrFail()
                        ->{$request->viaRelationship}()
                        ->save($model);
            } else {
                $model->save();
            }

            collect($callbacks)->each->__invoke();

            return $model;
        });
    }
}
