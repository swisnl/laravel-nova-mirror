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
            return null;
        })->preview(function () {
            return null;
        })->download(function () {
            return Storage::disk($this->disk)->download($this->value);
        })->delete(function () {
            if ($this->value) {
                Storage::disk($this->disk)->delete($this->value);
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
            if ($request->{$this->attribute}) {
                return $request->{$this->attribute}->store('/', $this->disk);
            }
        };
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
     * @return void
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (empty($request->{$requestAttribute})) {
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
    }

    /**
     * Create an HTTP response to download the underlying field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function toDownloadResponse(NovaRequest $request)
    {
        return call_user_func($this->downloadResponseCallback, $request);
    }

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge([
            'thumbnailUrl' => call_user_func($this->thumbnailUrlCallback),
            'previewUrl' => call_user_func($this->previewUrlCallback),
            'downloadable' => isset($this->downloadResponseCallback) && ! empty($this->value),
            'deletable' => isset($this->deleteCallback) && $this->deletable,
        ], $this->meta);
    }
}
