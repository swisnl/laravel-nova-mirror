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
        return (new GlobalSearch(
            $request, Nova::globallySearchableResources($request)
        ))->get();
    }
}
