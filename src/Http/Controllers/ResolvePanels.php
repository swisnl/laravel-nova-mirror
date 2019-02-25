<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;

trait ResolvePanels
{
    protected function defaultNameFor(Resource $resource)
    {
        return $resource->singularLabel().' '.__('Details');
    }

    protected function addDefaultPanel(NovaRequest $request, Collection $panels)
    {
        $default = $this->defaultNameFor($request->newResource());

        return $panels->when($panels->where('name', $default)->isEmpty(), function ($panels) use ($default) {
            return $panels->push((new Panel($default))->withToolbar());
        })->all();
    }

    /**
     * Assign any un-assigned fields to the default panel.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array  $response
     * @return array
     */
    protected function assignFieldsToPanels(NovaRequest $request, array $response)
    {
        $resource = $request->newResource();

        foreach ($response['fields'] as $field) {
            $field->panel = $field->panel ?? $this->defaultNameFor($resource);
        }

        return $response;
    }
}
