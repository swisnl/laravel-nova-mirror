<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Nova;

class DashboardCardRequest extends NovaRequest
{
    /**
     * Get all of the possible cards for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableCards()
    {
        return Nova::availableDashboardCards($this);
    }
}
