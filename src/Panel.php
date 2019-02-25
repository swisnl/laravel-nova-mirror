<?php

namespace Laravel\Nova;

use JsonSerializable;
use Illuminate\Http\Resources\MergeValue;

class Panel extends MergeValue implements JsonSerializable
{
    use Metable;

    /**
     * The name of the panel.
     *
     * @var string
     */
    public $name;

    /**
     * The panel fields.
     *
     * @var array
     */
    public $data;

    /**
     * The panel's component.
     */
    public $component = 'panel';

    /**
     * Indicates whether the detail toolbar should be visible on this panel.
     *
     * @var bool
     */
    public $showToolbar = false;

    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct($name, $fields = [])
    {
        $this->name = $name;

        parent::__construct($this->prepareFields($fields));
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array  $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        return collect(is_callable($fields) ? $fields() : $fields)->each(function ($field) {
            $field->panel = $this->name;
        })->all();
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameFor(Resource $resource)
    {
        return $resource->singularLabel().' '.__('Details');
    }

    /**
     * Display the toolbar when showing this panel.
     *
     * @return $this
     */
    public function withToolbar()
    {
        $this->showToolbar = true;

        return $this;
    }

    /**
     * Set the Vue component key for the panel.
     *
     * @param string $component
     * @return $this
     */
    public function withComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get the Vue component key for the panel.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'component' => $this->component(),
            'name' => $this->name,
            'showToolbar' => $this->showToolbar,
        ], $this->meta());
    }
}
