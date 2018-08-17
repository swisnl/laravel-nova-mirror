<?php

namespace Laravel\Nova\Fields;

class Currency extends Number
{
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

        $this->withMeta([
            'step' => '0.01',
            'format' => '%i',
        ]);

        $this->displayUsing(function ($value) {
            return $value ? money_format($this->meta['format'], $value) : null;
        });
    }

    /**
     * The format the field will used to be displayed in.
     *
     * @param  mixed  $step
     * @return $this
     */
    public function format($format)
    {
        return $this->withMeta(['format' => $format]);
    }
}
