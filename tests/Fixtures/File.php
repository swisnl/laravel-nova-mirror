<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Actions\Actionable;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use Actionable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [];

    public function setFilesAttribute($value)
    {
        if(!is_string($value) || strpos($value,'avatars') !== 0){
            throw new \RuntimeException('Invalid argument');
        }
    }
}
