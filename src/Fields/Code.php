<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;

class Code extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'code-field';

    /**
     * Indicates if the field is used to manipulate JSON.
     *
     * @var bool
     */
    public $json = false;

    /**
     * The JSON encoding options.
     *
     * @var int|null
     */
    public $jsonOptions;

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        $value = parent::resolveAttribute($resource, $attribute);

        if ($this->json) {
            return is_array($value) || is_object($value)
                    ? json_encode($value, $this->jsonOptions ?? JSON_PRETTY_PRINT)
                    : json_encode(json_decode($value), $this->jsonOptions ?? JSON_PRETTY_PRINT);
        }

        return $value;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = $this->json
                        ? json_decode($request[$requestAttribute], true)
                        : $request[$requestAttribute];
        }
    }

    /**
     * Indicate that the code field is used to manipulate JSON.
     *
     * @param  int|null  $options
     * @return $this
     */
    public function json($options = null)
    {
        $this->json = true;

        $this->jsonOptions = $options ?? JSON_PRETTY_PRINT;

        return $this->options(['mode' => 'application/json']);
    }

    /**
     * Define the language syntax highlighting mode for the field.
     *
     * @param  string  $language
     * @return $this
     */
    public function language($language)
    {
        return $this->options(['mode' => $language]);
    }

    /**
     * Set configuration options for the code editor instance.
     *
     * @param  array  $options
     * @return $this
     */
    public function options($options)
    {
        $currentOptions = $this->meta['options'] ?? [];

        return $this->withMeta([
            'options' => array_merge($currentOptions, $options),
        ]);
    }
}
