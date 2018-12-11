<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Contracts\Deletable as DeletableContract;

class File extends Field implements DeletableContract
{
    use Deletable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'file-field';

    /**
     * The callback that should be executed to store the file.
     *
     * @var callable
     */
    public $storageCallback;

    /**
     * The callback used to retrieve the thumbnail URL.
     *
     * @var callable
     */
    public $thumbnailUrlCallback;

    /**
     * The callback used to retrieve the preview URL.
     *
     * @var callable
     */
    public $previewUrlCallback;

    /**
     * The callback used to generate the download HTTP response.
     *
     * @var callable
     */
    public $downloadResponseCallback;

    /**
     * The name of the disk the file uses by default.
     *
     * @var string
     */
    public $disk;

    /**
     * The file storage path.
     *
     * @var string
     */
    public $storagePath = '/';

    /**
     * The callback that should be used to determine the file's storage name.
     *
     * @var callable|null
     */
    public $storeAsCallback;

    /**
     * The column where the file's original name should be stored.
     *
     * @var string
     */
    public $originalNameColumn;

    /**
     * The column where the file's size should be stored.
     *
     * @var string
     */
    public $sizeColumn;

    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string  $attribute
     * @param  string|null  $disk
     * @param  callable|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->disk = $disk;

        $this->prepareStorageCallback($storageCallback);

        $this->thumbnail(function () {
            //
        })->preview(function () {
            //
        })->download(function ($request, $model) {
            $name = $this->originalNameColumn ? $model->{$this->originalNameColumn} : null;

            return Storage::disk($this->disk)->download($this->value, $name);
        })->delete(function () {
            if ($this->value) {
                Storage::disk($this->disk)->delete($this->value);

                return $this->columnsThatShouldBeDeleted();
            }
        });
    }

    /**
     * Prepare the storage callback.
     *
     * @param  callable|null  $storageCallback
     * @return void
     */
    protected function prepareStorageCallback($storageCallback)
    {
        $this->storageCallback = $storageCallback ?? function ($request, $model) {
            return $this->mergeExtraStorageColumns($request, [
                $this->attribute => $this->storeFile($request),
            ]);
        };
    }

    /**
     * Store the file on disk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function storeFile($request)
    {
        if (! $this->storeAsCallback) {
            return $request->file($this->attribute)->store($this->storagePath, $this->disk);
        }

        return $request->file($this->attribute)->storeAs(
            $this->storagePath, call_user_func($this->storeAsCallback, $request), $this->disk
        );
    }

    /**
     * Merge the specified extra file information columns into the storable attributes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $attributes
     * @return array
     */
    protected function mergeExtraStorageColumns($request, array $attributes)
    {
        $file = $request->file($this->attribute);

        if ($this->originalNameColumn) {
            $attributes[$this->originalNameColumn] = $file->getClientOriginalName();
        }

        if ($this->sizeColumn) {
            $attributes[$this->sizeColumn] = $file->getSize();
        }

        return $attributes;
    }

    /**
     * Get an array of the columns that should be deleted and their values.
     *
     * @return array
     */
    protected function columnsThatShouldBeDeleted()
    {
        $attributes = [$this->attribute => null];

        if ($this->originalNameColumn) {
            $attributes[$this->originalNameColumn] = null;
        }

        if ($this->sizeColumn) {
            $attributes[$this->sizeColumn] = null;
        }

        return $attributes;
    }

    /**
     * Set the name of the disk the file is stored on by default.
     *
     * @param  string  $disk
     * @return $this
     */
    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Specify the callback that should be used to store the file.
     *
     * @param  callable  $storageCallback
     * @return $this
     */
    public function store(callable $storageCallback)
    {
        $this->storageCallback = $storageCallback;

        return $this;
    }

    /**
     * Set the file's storage path.
     *
     * @param  string  $path
     * @return $this
     */
    public function path($path)
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * Specify the callback that should be used to determine the file's storage name.
     *
     * @param  callable  $storeAsCallback
     * @return $this
     */
    public function storeAs(callable $storeAsCallback)
    {
        $this->storeAsCallback = $storeAsCallback;

        return $this;
    }

    /**
     * Specify the callback that should be used to retrieve the thumbnail URL.
     *
     * @param  callable  $thumbnailUrlCallback
     * @return $this
     */
    public function thumbnail(callable $thumbnailUrlCallback)
    {
        $this->thumbnailUrlCallback = $thumbnailUrlCallback;

        return $this;
    }

    /**
     * Resolve the thumbnail URL for the field.
     *
     * @return string|null
     */
    public function resolveThumbnailUrl()
    {
        return call_user_func($this->thumbnailUrlCallback, $this->value, $this->disk);
    }

    /**
     * Resolve the preview URL for the field.
     *
     * @return string|null
     */
    public function resolvePreviewUrl()
    {
        return call_user_func($this->previewUrlCallback, $this->value, $this->disk);
    }

    /**
     * Specify the callback that should be used to retrieve the preview URL.
     *
     * @param  callable  $previewUrlCallback
     * @return $this
     */
    public function preview(callable $previewUrlCallback)
    {
        $this->previewUrlCallback = $previewUrlCallback;

        return $this;
    }

    /**
     * Specify the callback that should be used to create a download HTTP response.
     *
     * @param  callable  $downloadResponseCallback
     * @return $this
     */
    public function download(callable $downloadResponseCallback)
    {
        $this->downloadResponseCallback = $downloadResponseCallback;

        return $this;
    }

    /**
     * Specify the column where the file's original name should be stored.
     *
     * @param  string  $column
     * @return $this
     */
    public function storeOriginalName($column)
    {
        $this->originalNameColumn = $column;

        return $this;
    }

    /**
     * Specify the column where the file size should be stored.
     *
     * @param  string  $column
     * @return $this
     */
    public function storeSize($column)
    {
        $this->sizeColumn = $column;

        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @return void
     */
    public function fillForAction(NovaRequest $request, $model)
    {
        if (isset($request[$this->attribute])) {
            $model->{$this->attribute} = $request[$this->attribute];
        }
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (is_null($file = $request->file($requestAttribute)) || ! $file->isValid()) {
            return;
        }

        $result = call_user_func($this->storageCallback, $request, $model);

        if ($result === true) {
            return;
        }

        if (! is_array($result)) {
            return $model->{$attribute} = $result;
        }

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }

        if ($this->isPrunable()) {
            return function () use ($model, $request) {
                call_user_func($this->deleteCallback, $request, $model);
            };
        }
    }

    /**
     * Create an HTTP response to download the underlying field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function toDownloadResponse(NovaRequest $request, $resource)
    {
        return call_user_func(
            $this->downloadResponseCallback, $request, $resource->resource
        );
    }

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'thumbnailUrl' => $this->resolveThumbnailUrl(),
            'previewUrl' => $this->resolvePreviewUrl(),
            'downloadable' => isset($this->downloadResponseCallback) && ! empty($this->value),
            'deletable' => isset($this->deleteCallback) && $this->deletable,
        ], $this->meta);
    }
}
