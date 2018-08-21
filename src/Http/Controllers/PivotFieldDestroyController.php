<?php

namespace Laravel\Nova\Http\Controllers;

use Laravel\Nova\DeleteField;
use Illuminate\Routing\Controller;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\PivotFieldDestroyRequest;

class PivotFieldDestroyController extends Controller
{
    /**
     * Delete the file at the given field.
     *
     * @param  \Laravel\Nova\Http\Requests\PivotFieldDestroyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(PivotFieldDestroyRequest $request)
    {
        $request->authorizeForAttachment();

        DeleteField::forRequest(
            $request, $request->findFieldOrFail(),
            $pivot = $request->findPivotModel()
        )->save();

        ActionEvent::forAttachedResourceUpdate(
            $request, $request->findModelOrFail(), $pivot
        )->save();
    }
}
