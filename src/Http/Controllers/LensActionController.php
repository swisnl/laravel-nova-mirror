<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\LensActionRequest;

class LensActionController extends Controller
{
    /**
     * Perform an action on the specified resources.
     *
     * @param  \Laravel\Nova\Http\Requests\LensActionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LensActionRequest $request)
    {
        return $request->action()->handleRequest($request);
    }
}
