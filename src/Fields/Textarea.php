<?php

namespace Laravel\Nova\Fields;

class Textarea extends Field
{
    use Expandable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'textarea-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * The number of rows used for the textarea.
     *
     * @var integer
     */
    public $rows = 5;

    /**
     * Set the number of rows used for the textarea.
     *
     * @param  integer $rows
     * @return $this
     */
    public function rows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'rows' => $this->rows,
            'shouldShow' => $this->shouldBeExpanded(),
        ]);
    }
}
