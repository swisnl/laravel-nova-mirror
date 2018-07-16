<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Nova;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class StyleController extends Controller
{
    /**
     * Serve the requested stylesheet.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(NovaRequest $request)
    {
        return response(
            file_get_contents(Nova::allStyles()[$request->style]),
            200, ['Content-Type' => 'text/css']
        );
    }
}
