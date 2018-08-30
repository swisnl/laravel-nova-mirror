<?php

namespace Laravel\Nova\Trix;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;

class DeleteAttachments
{
    /**
     * The field instance.
     *
     * @var \Laravel\Nova\Fields\Trix
     */
    public $field;

    /**
     * Create a new invokable instance.
     *
     * @param  \Laravel\Nova\Fields\Trix  $field
     * @return void
     */
    public function __construct(Trix $field)
    {
        $this->field = $field;
    }

    /**
     * Delete the attachments associated with the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $model
     * @return void
     */
    public function __invoke(Request $request, $model)
    {
        Attachment::where('attachable_type', get_class($model))
                ->where('attachable_id', $model->getKey())
                ->get()
                ->each
                ->purge($this->field);
    }
}
