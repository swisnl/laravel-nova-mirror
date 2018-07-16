<?php

namespace Laravel\Nova\Metrics;

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
