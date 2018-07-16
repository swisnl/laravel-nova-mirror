<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Collection;

class FieldCollection extends Collection
{
    /**
     * Find a given field by its attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $default
     * @return \Laravel\Nova\Fields\Field|null
     */
    public function findFieldByAttribute($attribute, $default = null)
    {
        return $this->first(function ($field) use ($attribute) {
            return isset($field->attribute) &&
                   $field->attribute == $attribute;
        }, $default);
    }
}
