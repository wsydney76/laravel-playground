<?php

namespace App\Concerns;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;

trait HandlesMediaUpload
{
    /**
     * Sync a single-file media collection on a model:
     *  - Upload & replace when a new file is provided.
     *  - Clear the collection when $delete is true and no new file is given.
     *  - Do nothing otherwise.
     */
    public function syncMedia(
        HasMedia $model,
        string $collection,
        string|UploadedFile|null $file,
        bool $delete = false,
    ): void {
        if ($file) {
            $model->clearMediaCollection($collection);
            $model->addMedia($file)->toMediaCollection($collection);
        } elseif ($delete) {
            $model->clearMediaCollection($collection);
        }
    }
}
