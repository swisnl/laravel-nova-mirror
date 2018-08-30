<?php

namespace Laravel\Nova\Trix;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;

class DiscardPendingAttachments
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
     * Discard pendings attachments on the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        PendingAttachment::where('draft_id', $request->draftId)
                    ->get()
                    ->each
                    ->purge($this->field);
    }
}
