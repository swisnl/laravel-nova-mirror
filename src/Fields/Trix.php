<?php

namespace Laravel\Nova\Fields;

class Trix extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'trix-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * The callback that should be executed to store file attachments.
     *
     * @var callable
     */
    public $attachCallback;

    /**
     * Specify the callback that should be used to store file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function attach(callable $callback)
    {
        $this->attachCallback = $callback;

        return $this;
    }
}
