<?php

namespace Laravel\Nova\Actions;

trait Actionable
{
    /**
     * Get all of the action events for the user.
     */
    public function actions()
    {
        return $this->morphMany(ActionEvent::class, 'actionable');
    }
}
