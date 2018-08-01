<?php

namespace Laravel\Nova\Console\Concerns;

trait AcceptsNameAndVendor
{
    public function hasValidNameArgument()
    {
        $name = $this->argument('name');

        if (! str_contains('/', $name)) {
            $this->line('');

            $this->error("The name argument expects a full namespace.");

            return false;
        }

        return true;
    }
}