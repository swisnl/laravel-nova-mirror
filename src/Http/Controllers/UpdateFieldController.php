<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class UpdateFieldController extends Controller
{
    use ResolvePanels;

    /**
     * List the update fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->newResourceWith($request->findModelOrFail());

        $resource->authorizeToUpdate($request);

        return response()->json($this->assignFieldsToPanels($request, [
            'panels' => $this->addDefaultPanel($request, $resource->availablePanels($request)),
            'fields' => $resource->creationFields($request)->values()->all(),
        ]));
    }

    protected function defaultNameFor(Resource $resource)
    {
        return __('Edit').' '.$resource->singularLabel();
    }
}
