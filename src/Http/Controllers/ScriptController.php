<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\Nova;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class ScriptController extends Controller
{
    /**
     * Serve the requested script.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(NovaRequest $request)
    {
        return response(
            file_get_contents(Nova::allScripts()[$request->script]),
            200, ['Content-Type' => 'application/javascript']
        );
    }
}
