<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedAction extends Action implements ShouldQueue
{
    use SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $_SERVER['queuedAction.applied'][] = $models;
        $_SERVER['queuedAction.appliedFields'][] = $fields;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make('Test', 'test'),
        ];
    }
}
