<?php

namespace Laravel\Nova\Fields;

class Heading extends Field
{
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name = null, $attribute = null, $resolveCallback = null)
    {
        parent::__construct(null, $attribute, $resolveCallback);

        $this->hideFromIndex();
        $this->hideFromDetail();
        $this->withMeta(['asHtml' => false]);
    }

    /**
     * Display the content of the field as raw HTML using Vue.
     *
     * @param  string $content
     * @return $this
     */
    public function content($content)
    {
        return $this->withMeta(['value' => $content]);
    }

    /**
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        return $this->withMeta(['asHtml' => true]);
    }

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'info-field';
}
