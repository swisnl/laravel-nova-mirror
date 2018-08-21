<?php

namespace Laravel\Nova\Metrics;

use JsonSerializable;

class TrendResult implements JsonSerializable
{
    /**
     * The value of the result.
     *
     * @var string|null
     */
    public $value;

    /**
     * The trend data of the result.
     *
     * @var array
     */
    public $trend = [];

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
     * Create a new trend result instance.
     *
     * @param  string|null  $value
     * @return void
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Set the primary result amount for the trend.
     *
     * @param  string|null  $value
     * @return $this
     */
    public function result($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the latest value of the trend as the primary result.
     *
     * @return $this
     */
    public function showLatestValue()
    {
        if (is_array($this->trend)) {
            return $this->result(last($this->trend));
        }

        return $this;
    }

    /**
     * Set the trend of data for the metric.
     *
     * @param  array  $trend
     * @return $this
     */
    public function trend(array $trend)
    {
        $this->trend = $trend;

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
        return $this->prefix($symbol);
    }

    /**
     * Indicate that the metric represents a euro value.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function euros($symbol = '€')
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
     * Prepare the metric result for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'trend' => $this->trend,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
        ];
    }
}
