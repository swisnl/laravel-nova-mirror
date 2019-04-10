<?php

namespace Laravel\Nova\Metrics;

class PartitionColors
{
    /**
     * @var array
     */
    public $colors;

    private $pointer = 0;

    public function __construct(array $colors = [])
    {
        $this->colors = $colors;

        return $this;
    }

    public function get($label)
    {
        return $this->colors[$label] ?? $this->next();
    }

    protected function next()
    {
        return blank($this->colors) ? null :
            tap($this->colors[
                $this->pointer % count($this->colors)
            ], function () {
                $this->pointer++;
            });
    }
}
