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

    /**
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        return $this->withMeta(['asHtml' => true]);
    }
}
