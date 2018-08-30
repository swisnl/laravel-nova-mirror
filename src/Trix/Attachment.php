<?php

namespace Laravel\Nova\Trix;

use Laravel\Nova\Fields\Trix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nova_trix_attachments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Purge the attachment.
     *
     * @param  \Laravel\Nova\Fields\Trix  $field
     * @return void
     */
    public function purge(Trix $field)
    {
        Storage::disk($field->disk)->delete($this->attachment);

        $this->delete();
    }
}
