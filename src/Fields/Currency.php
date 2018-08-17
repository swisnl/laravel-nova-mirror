<?php

namespace Laravel\Nova\Fields;

class Currency extends Number
{
    /**
     * The format the field will be displayed in.
     *
     * @var string
     */
    public $format;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->step('0.01')->displayUsing(function ($value) {
            return $value ? money_format($this->format ?? '%i', $value) : null;
        });
    }

    /**
     * The monetary format the field will used be displayed in.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }
}
