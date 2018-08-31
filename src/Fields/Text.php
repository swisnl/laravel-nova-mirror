<?php

namespace Laravel\Nova\Fields;

class Text extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'text-field';

    protected $asHtml = false;

    /**
     * Display the field as raw HTML inside Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        $this->asHtml = true;

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'asHtml' => $this->asHtml,
        ]);
    }
}
