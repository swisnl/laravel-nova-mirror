<?php

namespace Laravel\Nova\Trix;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;

class DetachAttachment
{
    /**
     * Delete an attachment from the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        Attachment::where('url', $request->attachmentUrl)
                        ->get()
                        ->each
                        ->purge();
    }
}
