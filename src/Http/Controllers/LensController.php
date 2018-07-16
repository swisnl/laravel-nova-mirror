<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\LensRequest;

class LensController extends Controller
{
    /**
     * List the lenses for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(LensRequest $request)
    {
        return $request->availableLenses();
    }

    /**
     * Get the specified lens and its resources.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(LensRequest $request)
    {
        $paginator = $request->lens()->query($request, $request->newQuery());

        if ($paginator instanceof Builder) {
            $paginator = $paginator->simplePaginate($request->perPage ?? 25);
        }

        return response()->json([
            'name' => $request->lens()->name(),
            'resources' => $request->toResources($paginator->getCollection()),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'softDeletes' => $request->resourceSoftDeletes(),
        ]);
    }
}
