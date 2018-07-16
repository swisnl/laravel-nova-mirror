<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\DashboardCardRequest;

class DashboardCardController extends Controller
{
    /**
     * List the cards for the dashboard.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardCardRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(DashboardCardRequest $request)
    {
        return $request->availableCards();
    }
}
