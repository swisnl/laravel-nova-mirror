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

        return response()->json(['url' => call_user_func(
            $field->attachCallback, $request
        )]);
    }

    /**
     * Purge pending attachments for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(NovaRequest $request)
    {
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        call_user_func(
            $field->discardCallback, $request
        );
    }
}
