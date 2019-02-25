<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Resource;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreationFieldController extends Controller
{
    use ResolvePanels;

    /**
     * List the creation fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        $resourceClass = $request->resource();

        $resourceClass::authorizeToCreate($request);

        $resource = $request->newResource();

        return response()->json($this->assignFieldsToPanels($request, [
            'panels' => $this->addDefaultPanel($request, $resource->availablePanels($request)),
            'fields' => $resource->creationFields($request)->values()->all(),
        ]));
    }

    protected function defaultNameFor(Resource $resource)
    {
        return __('New').' '.$resource->singularLabel();
    }
}
