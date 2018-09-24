<?php

namespace Laravel\Nova\Fields;

trait Expandable
{
    /**
     * Disable automatic hiding of textarea fields inside Nova.
     *
     * @return $this
     */
    public function alwaysShow()
    {
        return $this->withMeta(['alwaysShow' => true]);
    }
}
