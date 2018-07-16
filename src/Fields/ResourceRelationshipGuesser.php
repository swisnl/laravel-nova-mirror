<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Str;

class ResourceRelationshipGuesser
{
    /**
     * Guess the resource class name from the displayable name.
     *
     * @param  string  $name
     * @return string
     */
    public static function guessResource($name)
    {
        $results = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);

        return str_replace(
            class_basename($results[3]['class']),
            Str::singular($name),
            $results[3]['class']
        );
    }
}
