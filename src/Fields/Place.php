<?php

namespace Laravel\Nova\Fields;

class Place extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'place-field';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->secondAddressLine('address_line_2')
             ->city('city')
             ->state('state')
             ->postalCode('postal_code')
             ->country('country');
    }

    /**
     * Instruct the field to only display cities in its results.
     *
     * @return $this
     */
    public function onlyCities()
    {
        return $this->type('city');
    }

    /**
     * Set the place type.
     *
     * @param  string  $type
     * @return $this
     */
    public function type($type)
    {
        if ($type == 'city') {
            $this->secondAddressLine(null)->city(null)->postalCode(null);
        }

        return $this->withMeta(['placeType' => $type]);
    }

    /**
     * Set the countries to search within.
     *
     * @param  array  $countries
     * @return $this
     */
    public function countries(array $countries)
    {
        return $this->withMeta(['countries' => $countries]);
    }

    /**
     * Specify the field that contains the second address line.
     *
     * @param  string  $field
     * @return $this
     */
    public function secondAddressLine($field)
    {
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Specify the field that contains the city.
     *
     * @param  string  $field
     * @return $this
     */
    public function city($field)
    {
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Specify the field that contains the state.
     *
     * @param  string  $field
     * @return $this
     */
    public function state($field)
    {
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Specify the field that contains the postal code.
     *
     * @param  string  $field
     * @return $this
     */
    public function postalCode($field)
    {
        return $this->withMeta([__FUNCTION__ => $field]);
    }

    /**
     * Specify the field that contains the country.
     *
     * @param  string  $field
     * @return $this
     */
    public function country($field)
    {
        return $this->withMeta([__FUNCTION__ => $field]);
    }
}
