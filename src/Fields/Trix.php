<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Trix\DetachAttachment;
use Laravel\Nova\Trix\DeleteAttachments;
use Laravel\Nova\Trix\PendingAttachment;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Trix\StorePendingAttachment;
use Laravel\Nova\Trix\DiscardPendingAttachments;
use Laravel\Nova\Contracts\Deletable as DeletableContract;

class Trix extends Field implements DeletableContract
{
    use Deletable, Expandable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'trix-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Indicates if the field should accept files.
     *
     * @var bool
     */
    public $withFiles = false;

    /**
     * The disk that should be used to store files.
     *
     * @var string
     */
    public $disk = 'public';

    /**
     * The callback that should be executed to store file attachments.
     *
     * @var callable
     */
    public $attachCallback;

    /**
     * The callback that should be executed to delete persisted file attachments.
     *
     * @var callable
     */
    public $detachCallback;

    /**
     * The callback that should be executed to discard file attachments.
     *
     * @var callable
     */
    public $discardCallback;

    /**
     * The disk that should be used to store attachments.
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
     * Specify the callback that should be used to store file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function attach(callable $callback)
    {
        $this->withFiles = true;

        $this->attachCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to delete a single, persisted file attachment.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function detach(callable $callback)
    {
        $this->withFiles = true;

        $this->detachCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to discard pending file attachments.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function discard(callable $callback)
    {
        $this->withFiles = true;

        $this->discardCallback = $callback;

        return $this;
    }

    /**
     * Specify the callback that should be used to delete the field.
     *
     * @param  callable  $deleteCallback
     * @return $this
     */
    public function delete(callable $deleteCallback)
    {
        $this->withFiles = true;

        $this->deleteCallback = $deleteCallback;

        return $this;
    }

    /**
     * Specify that file uploads should not be allowed.
     *
     * @param  string  $disk
     * @return $this
     */
    public function withFiles($disk = null)
    {
        $this->withFiles = true;

        $this->disk($disk);

        $this->attach(new StorePendingAttachment($this))
             ->detach(new DetachAttachment($this))
             ->delete(new DeleteAttachments($this))
             ->discard(new DiscardPendingAttachments($this))
             ->prunable();

        return $this;
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
        parent::fillAttribute($request, $requestAttribute, $model, $attribute);

        if ($request->{$this->attribute.'DraftId'} && $this->withFiles) {
            return function () use ($request, $model, $attribute) {
                PendingAttachment::persistDraft(
                    $request->{$this->attribute.'DraftId'},
                    $this,
                    $model
                );
            };
        }
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
            'withFiles' => $this->withFiles,
        ]);
    }
}
