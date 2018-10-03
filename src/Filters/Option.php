<?php

namespace Laravel\Nova\Filters;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Laravel\Nova\ProxiesCanSeeToGate;

class Option
{
    public $label;

    public $key;

    public $trueValue;

    public $falseValue;

    public function __construct($label, $key = '')
    {
        $this->label = $label;
        $this->key = $key ?? str_replace(' ', '_', Str::lower($name));
    }

    public static function make($label, $key = '')
    {
        return new static($label, $key);
    }

    public function trueValue($value)
    {
        $this->trueValue = $value;

        return $this;
    }

    public function falseValue($value)
    {
        $this->falseValue = $value;

        return $this;
    }

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
