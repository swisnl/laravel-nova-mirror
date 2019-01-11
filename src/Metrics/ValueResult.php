<?php

namespace Laravel\Nova\Metrics;

use JsonSerializable;

class ValueResult implements JsonSerializable
{
    /**
     * The value of the result.
     *
     * @var mixed
     */
    public $value;

    /**
     * The previous value.
     *
     * @var mixed
     */
    public $previous;

    /**
     * The previous value label.
     *
     * @var string
     */
    public $previousLabel;

    /**
     * The metric value prefix.
     *
     * @var string
     */
    public $prefix;

    /**
     * The metric value suffix.
     *
     * @var string
     */
    public $suffix;

    /**
     * The metric value formatting.
     *
     * @var string
     */
    public $format;

    /**
     * Create a new value result instance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Set the previous value for the metric.
     *
     * @param  mixed  $previous
     * @param  string  $label
     * @return $this
     */
    public function previous($previous, $label = null)
    {
        $this->previous = $previous;
        $this->previousLabel = $label;

        return $this;
    }

    /**
     * Indicate that the metric represents a dollar value.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function dollars($symbol = '$')
    {
        return $this->currency($symbol);
    }

    /**
     * Indicate that the metric represents a currency value.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function currency($symbol = '$')
    {
        return $this->prefix($symbol);
    }

    /**
     * Set the metric value prefix.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set the metric value suffix.
     *
     * @param  string  $suffix
     * @return $this
     */
    public function suffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Set the metric value formatting.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Prepare the metric result for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'previous' => $this->previous,
            'previousLabel' => $this->previousLabel,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'format' => $this->format,
        ];
    }
}
