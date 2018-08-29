<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class TrixAttachmentController extends Controller
{
    /**
     * Store an attachment for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NovaRequest $request)
    {
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        return call_user_func(
            $field->attachCallback, $request
        );
    }
}
