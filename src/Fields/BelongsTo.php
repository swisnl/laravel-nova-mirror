<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\TrashedStatus;
use Laravel\Nova\Rules\Relatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class BelongsTo extends Field
{
    use FormatsRelatableDisplayValues;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'belongs-to-field';

    /**
     * The class name of the related resource.
     *
     * @var string
     */
    public $resourceClass;

    /**
     * The URI key of the related resource.
     *
     * @var string
     */
    public $resourceName;

    /**
     * The name of the Eloquent "belongs to" relationship.
     *
     * @var string
     */
    public $belongsToRelationship;

    /**
     * The key of the related Eloquent model.
     *
     * @var string
     */
    public $belongsToId;

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;

    /**
     * Indicates if the field is nullable.
     *
     * @var bool
     */
    public $nullable = false;

    /**
     * Indicates if this relationship is searchable.
     *
     * @var bool
     */
    public $searchable = false;

    /**
     * The callback that should be run when the field is filled.
     *
     * @var \Closure
     */
    public $filledCallback;

    /**
     * The attribute that is the inverse of this relationship.
     *
     * @var string
     */
    public $inverse;

    /**
     * The displayable singular label of the relation.
     *
     * @var string
     */
    public $singularLabel;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute);

        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->belongsToRelationship = $this->attribute;
    }

    /**
     * Determine if the field should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return $this->isNotRedundant($request) && call_user_func(
            [$this->resourceClass, 'authorizedToViewAny'], $request
        ) && parent::authorize($request);
    }

    /**
     * Determine if the field is not redundant.
     *
     * Ex: Is this a "user" belongs to field in a blog post list being shown on the "user" detail page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function isNotRedundant(Request $request)
    {
        return (! $request instanceof ResourceIndexRequest || ! $request->viaResource) ||
               ($this->resourceName !== $request->viaResource);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $value = $resource->{$this->attribute}()->withoutGlobalScopes()->first();

        if ($value) {
            $this->belongsToId = $value->getKey();

            $this->value = $this->formatDisplayValue($value);
        }
    }

    /**
     * Get the validation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        $query = $this->buildAssociatableQuery(
            $request, $request->{$this->attribute.'_trashed'} === 'true'
        );

        return array_merge_recursive(parent::getRules($request), [
            $this->attribute => array_filter([
                $this->nullable ? 'nullable' : 'required',
                new Relatable($request, $query),
            ]),
        ]);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @return void
     */
    public function fill(NovaRequest $request, $model)
    {
        parent::fillInto($request, $model, $model->{$this->attribute}()->getForeignKey());

        if ($this->filledCallback) {
            call_user_func($this->filledCallback, $request, $model);
        }
    }

    /**
     * Build an associatable query for the field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $model = forward_static_call(
            [$resourceClass = $this->resourceClass, 'newModel']
        );

        $query = $request->first === 'true'
                        ? $model->newQueryWithoutScopes()->whereKey($request->current)
                        : $resourceClass::buildIndexQuery(
                                $request, $model->newQuery(), $request->search,
                                [], [], TrashedStatus::fromBoolean($withTrashed)
                          );

        return $query->tap(function ($query) use ($request, $model) {
            forward_static_call($this->associatableQueryCallable($request, $model), $request, $query);
        });
    }

    /**
     * Get the associatable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function associatableQueryCallable(NovaRequest $request, $model)
    {
        return ($method = $this->associatableQueryMethod($request, $model))
                    ? [$request->resource(), $method]
                    : [$this->resourceClass, 'relatableQuery'];
    }

    /**
     * Get the associatable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    protected function associatableQueryMethod(NovaRequest $request, $model)
    {
        $method = 'relatable'.Str::plural(class_basename($model));

        if (method_exists($request->resource(), $method)) {
            return $method;
        }
    }

    /**
     * Format the given associatable resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return array
     */
    public function formatAssociatableResource(NovaRequest $request, $resource)
    {
        return array_filter([
            'avatar' => $resource->resolveAvatarUrl($request),
            'display' => $this->formatDisplayValue($resource),
            'value' => $resource->getKey(),
        ]);
    }

    /**
     * Specify if the relationship should be searchable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function searchable($value = true)
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Specify a callback that should be run when the field is filled.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function filled($callback)
    {
        $this->filledCallback = $callback;

        return $this;
    }

    /**
     * Set the attribute name of the inverse of the relationship.
     *
     * @param  string  $inverse
     * @return $this
     */
    public function inverse($inverse)
    {
        $this->inverse = $inverse;

        return $this;
    }

    /**
     * Indicate that the field should be nullable.
     *
     * @param  bool  $nullable
     * @return $this
     */
    public function nullable($nullable = true)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Set the displayable singular label of the resource.
     *
     * @return string
     */
    public function singularLabel($singularLabel)
    {
        $this->singularLabel = $singularLabel;

        return $this;
    }

    /**
     * Get additional meta information to merge with the field payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'resourceName' => $this->resourceName,
            'label' => forward_static_call([$this->resourceClass, 'label']),
            'singularLabel' => $this->singularLabel ?? $this->name ?? forward_static_call([$this->resourceClass, 'singularLabel']),
            'belongsToRelationship' => $this->belongsToRelationship,
            'belongsToId' => $this->belongsToId,
            'nullable' => $this->nullable,
            'searchable' => $this->searchable,
        ], $this->meta);
    }
}
