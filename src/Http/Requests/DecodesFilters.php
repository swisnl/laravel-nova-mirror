<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Query\ApplyFilter;

trait DecodesFilters
{
    /**
     * Get the filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function filters()
    {
        if (empty($filters = $this->decodedFilters())) {
            return collect();
        }

        $availableFilters = $this->availableFilters();

        return collect($filters)->filter(function ($filter) use ($availableFilters) {
            return $availableFilters->contains(function ($availableFilter) use ($filter) {
                return $filter['class'] === get_class($availableFilter);
            });
        })->map(function ($filter) {
            return new ApplyFilter($filter['class'], $filter['value']);
        })->values();
    }

    /**
     * Decode the filters specified for the request.
     *
     * @return array
     */
    protected function decodedFilters()
    {
        if (empty($this->filters)) {
            return [];
        }

        $filters = json_decode(base64_decode($this->filters), true);

        return is_array($filters) ? $filters : [];
    }

    /**
     * Get all of the possibly available filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableFilters()
    {
        return $this->newResource()->availableFilters($this);
    }
}
