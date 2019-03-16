<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\ActionModelCollection;

class RedirectAction extends Action
{
    public function handle(ActionFields $fields, ActionModelCollection $models)
    {
        return Action::redirect('http://yahoo.com');
    }
}
