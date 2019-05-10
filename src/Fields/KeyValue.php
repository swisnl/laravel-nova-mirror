<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;

class KeyValue extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'key-value-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * The label that should be used for the key heading.
     *
     * @var string
     */
    public $keyLabel;

    /**
     * The label that should be used for the value heading.
     *
     * @var string
     */
    public $valueLabel;

    /**
     * The label that should be used for the "add row" button.
     *
     * @var string
     */
    public $actionText;

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
            $model->{$attribute} = json_decode($request[$requestAttribute], true);
        }
    }

    /**
     * The label that should be used for the key table heading.
     *
     * @param string $label
     * @return $this
     */
    public function keyLabel($label)
    {
        $this->keyLabel = $label;

        return $this;
    }

    /**
     * The label that should be used for the value table heading.
     *
     * @param string $label
     * @return $this
     */
    public function valueLabel($label)
    {
        $this->valueLabel = $label;

        return $this;
    }

    /**
     * The label that should be used for the add row button.
     *
     * @param string $label
     * @return $this
     */
    public function actionText($label)
    {
        $this->actionText = $label;

        return $this;
    }

    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'keyLabel' => $this->keyLabel ?? __('Key'),
            'valueLabel' => $this->valueLabel ?? __('Value'),
            'actionText' => $this->actionText ?? __('Add row'),
        ]);
    }
}
