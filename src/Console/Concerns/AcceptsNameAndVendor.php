<?php

namespace Laravel\Nova\Console\Concerns;

trait AcceptsNameAndVendor
{
    public function hasValidNameArgument()
    {
        $name = $this->argument('name');

        if (! str_contains('/', $name)) {
            $this->line('');

            $this->error("The name argument expects the vendor and the name. Here's an example: `vendor/name`");

            return false;
        }

        return true;
    }
}