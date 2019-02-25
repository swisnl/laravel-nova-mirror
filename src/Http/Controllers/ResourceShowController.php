<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Resource;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;

class ResourceShowController extends Controller
{
    use ResolvePanels;

    /**
     * Display the resource for administration.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceDetailRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ResourceDetailRequest $request)
    {
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        $resource->authorizeToView($request);

        return response()->json([
            'panels' => $this->addDefaultPanel($request, $resource->availablePanels($request)),
            'resource' => $this->assignFieldsToPanels(
                $request, $resource->serializeForDetail($request)
            ),
        ]);
    }

    protected function defaultNameFor(Resource $resource)
    {
        return $resource->singularLabel().' '.__('Details');
    }
}
