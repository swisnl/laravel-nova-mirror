<?php

namespace Laravel\Nova\Trix;

use Illuminate\Http\Request;

class DeleteAttachments
{
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
                ->purge();
    }
}
