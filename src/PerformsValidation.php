<?php

namespace Laravel\Nova;

use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;

trait PerformsValidation
{
    /**
     * Validate a resource creation request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public static function validateForCreation(NovaRequest $request)
    {
        static::validatorForCreation($request)->validate();
    }

    /**
     * Create a validator instance for a resource creation request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForCreation(NovaRequest $request)
    {
        return Validator::make($request->all(), static::rulesForCreation($request))
                ->after(function ($validator) use ($request) {
                    static::afterValidation($request, $validator);
                    static::afterCreationValidation($request, $validator);
                });
    }

    /**
     * Get the validation rules for a resource creation request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public static function rulesForCreation(NovaRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->creationFields($request)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Get the creation validation rules for a specific field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $field
     * @return array
     */
    public static function creationRulesFor(NovaRequest $request, $field)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->availableFields($request)
                    ->where('attribute', $field)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Validate a resource update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public static function validateForUpdate(NovaRequest $request)
    {
        static::validatorForUpdate($request)->validate();
    }

    /**
     * Create a validator instance for a resource update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForUpdate(NovaRequest $request)
    {
        return Validator::make($request->all(), static::rulesForUpdate($request))
                ->after(function ($validator) use ($request) {
                    static::afterValidation($request, $validator);
                    static::afterUpdateValidation($request, $validator);
                });
    }

    /**
     * Get the validation rules for a resource update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public static function rulesForUpdate(NovaRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->updateFields($request)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Get the update validation rules for a specific field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $field
     * @return array
     */
    public static function updateRulesFor(NovaRequest $request, $field)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->availableFields($request)
                    ->where('attribute', $field)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Validate a resource attachment request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public static function validateForAttachment(NovaRequest $request)
    {
        static::validatorForAttachment($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForAttachment(NovaRequest $request)
    {
        return Validator::make($request->all(), static::rulesForAttachment($request));
    }

    /**
     * Get the validation rules for a resource attachment request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public static function rulesForAttachment(NovaRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->creationPivotFields($request, $request->relatedResource)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Validate a resource attachment update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public static function validateForAttachmentUpdate(NovaRequest $request)
    {
        static::validatorForAttachmentUpdate($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForAttachmentUpdate(NovaRequest $request)
    {
        return Validator::make($request->all(), static::rulesForAttachmentUpdate($request));
    }

    /**
     * Get the validation rules for a resource attachment update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public static function rulesForAttachmentUpdate(NovaRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->updatePivotFields($request, $request->relatedResource)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Perform any final formatting of the given validation rules.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array  $rules
     * @return array
     */
    protected static function formatRules(NovaRequest $request, array $rules)
    {
        $replacements = array_filter([
            '{{resourceId}}' => $request->resourceId,
        ]);

        if (empty($replacements)) {
            return $rules;
        }

        return collect($rules)->map(function ($rules) use ($replacements) {
            return collect($rules)->map(function ($rule) use ($replacements) {
                return is_string($rule)
                            ? str_replace(array_keys($replacements), array_values($replacements), $rule)
                            : $rule;
            })->all();
        })->all();
    }

    /**
     * Get the validation attribute for a specific field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $field
     * @return string
     */
    public static function validationAttributeFor(NovaRequest $request, $field)
    {
        return (new static(static::newModel()))
                    ->availableFields($request)
                    ->firstWhere('attribute', $field)
                    ->getValidationAttribute($request);
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterValidation(NovaRequest $request, $validator)
    {
        //
    }

    /**
     * Handle any post-creation validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterCreationValidation(NovaRequest $request, $validator)
    {
        //
    }

    /**
     * Handle any post-update validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterUpdateValidation(NovaRequest $request, $validator)
    {
        //
    }
}
