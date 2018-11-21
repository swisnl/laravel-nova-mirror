<?php

namespace Laravel\Nova\Filters;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Laravel\Nova\ProxiesCanSeeToGate;

class Option
{
    /**
     * The option's label.
     *
     * @var string
     */
    public $label;

    /**
     * The option's key.
     *
     * @var string
     */
    public $key;

    /**
     * The option's truthy value.
     *
     * @var mixed
     */
    public $trueValue;

    /**
     * The option's falsy value.
     *
     * @var mixed
     */
    public $falseValue;

    /**
     * Create a new filter option instance.
     *
     * @param  string  $label
     * @param  string  $key
     * @return void
     */
    public function __construct($label, $key = '')
    {
        $this->label = $label;

        $this->key = $key ?? str_replace(' ', '_', Str::lower($name));
    }

    /**
     * Create a new filter option instance.
     *
     * @param  string  $label
     * @param  string  $key
     * @return static
     */
    public static function make($label, $key = '')
    {
        return new static($label, $key);
    }

    /**
     * Set the truthy value for the filter.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function trueValue($value)
    {
        $this->trueValue = $value;

        return $this;
    }

    /**
     * Set the falsy value for the filter.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function falseValue($value)
    {
        $this->falseValue = $value;

        return $this;
    }

    /**
     * Convert the filter option to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->label,
            'key' => $this->key,
            'trueValue' => $this->trueValue ?? true,
            'falseValue' => $this->falseValue ?? false,
        ];
    }
}
