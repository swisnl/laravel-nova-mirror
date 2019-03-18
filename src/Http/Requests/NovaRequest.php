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

    /**
     * Determine if this request is an attach or create request.
     *
     * @return bool
     */
    public function isCreateOrAttachRequest()
    {
        return $this->editing && in_array($this->editMode, ['create', 'attach']);
    }

    /**
     * Determine if this request is an update or update-attached request.
     *
     * @return bool
     */
    public function isUpdateOrUpdateAttachedRequest()
    {
        return $this->editing && in_array($this->editMode, ['update', 'update-attached']);
    }
}
