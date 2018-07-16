<?php

namespace Laravel\Nova\Http\Requests;

use LogicException;
use Illuminate\Database\Eloquent\Builder;

class LensActionRequest extends ActionRequest
{
    use InteractsWithLenses;

    /**
     * Transform the request into a query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toQuery()
    {
        return tap($this->lens()->query(LensRequest::createFrom($this), $this->newQuery()), function ($query) {
            if (! $query instanceof Builder) {
                throw new LogicException('Lens must return an Eloquent query instance in order to apply actions.');
            }
        });
    }
}
