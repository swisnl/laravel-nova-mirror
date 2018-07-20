<?php

namespace Laravel\Nova\Metrics;

use Closure;
use JsonSerializable;

class PartitionResult implements JsonSerializable
{
    /**
     * The value of the result.
     *
     * @var array
     */
    public $value;

    /**
     * Create a new partition result instance.
     *
     * @param  array  $value
     * @return void
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }

    /**
     * Format the labels for the partition result.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function label(Closure $callback)
    {
        $this->value = collect($this->value)->mapWithKeys(function ($value, $label) use ($callback) {
            return [$callback($label) => $value];
        })->all();

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
            'value' => collect($this->value ?? [])->map(function ($value, $label) {
                return ['label' => $label, 'value' => $value];
            })->values()->all(),
        ];
    }
}
