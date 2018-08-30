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
     * The callback that should be executed to delete persisted file attachments.
     *
     * @var callable
     */
    public $deleteCallback;

    /**
     * The callback that should be executed to discard file attachments.
     *
     * @var callable
     */
    public $discardCallback;

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

    /**
     * Specify the callback that should be used to delete persisted file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function delete(callable $callback)
    {
        $this->deleteCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to discard pending file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function discard(callable $callback)
    {
        $this->discardCallback = $callback;

        return $this;
    }

    /**
     * Specify that file uploads should not be allowed.
     *
     * @return $this
     */
    public function withoutFiles()
    {
        return $this->withMeta(['acceptFiles' => false]);
    }
}
