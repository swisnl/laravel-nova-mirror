<?php

namespace Laravel\Nova\Fields;

class Textarea extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'textarea-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;
}
