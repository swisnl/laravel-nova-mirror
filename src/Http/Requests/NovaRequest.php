<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\MemoizesMethods;
use Illuminate\Foundation\Http\FormRequest;

class NovaRequest extends FormRequest
{
    use InteractsWithResources, InteractsWithRelatedResources, MemoizesMethods;

    /**
     * Determine if this request is via a many to many relationship.
     *
     * @return bool
     */
    public function viaManyToMany()
    {
        return in_array(
            $this->relationshipType,
            ['belongsToMany', 'morphToMany']
        );
    }
}
