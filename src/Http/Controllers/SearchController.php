<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Nova;
use Laravel\Nova\GlobalSearch;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class SearchController extends Controller
{
    /**
     * Get the global search results for the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(NovaRequest $request)
    {
        $resources = collect(Nova::availableResources($request))
                    ->filter(function ($resource) {
                        return true;
                        // return $resource::usesScout();
                    });

        return (new GlobalSearch($request, $resources))->get();
    }
}
