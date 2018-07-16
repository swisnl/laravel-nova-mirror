<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreationFieldController extends Controller
{
    /**
     * List the creation fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->resource();

        $resource::authorizeToCreate($request);

        return response()->json(
            $request->newResource()->creationFields($request)->values()->all()
        );
    }
}
