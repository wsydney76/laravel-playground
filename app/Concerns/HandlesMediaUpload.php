<?php

namespace App\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            // If a bare filename was passed (no directory separators), resolve the
            // full path from Livewire's temporary upload directory so that
            // addMedia() can locate the file.
            if (is_string($file) && !str_contains($file, DIRECTORY_SEPARATOR) && !str_contains($file, '/')) {
                $disk = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default', 'local');
                $directory = config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp';
                $file = Storage::disk($disk)->path($directory . '/' . $file);
            }

            $model->clearMediaCollection($collection);
            $model->addMedia($file)->toMediaCollection($collection);
        } elseif ($delete) {
            $model->clearMediaCollection($collection);
        }
    }

    /**
     * Sync a multi-file media collection on a model:
     *  - Delete individual items whose IDs appear in $deleteIds.
     *  - Append every file in $files to the collection.
     *
     * Pass an empty $files array and no $deleteIds to leave the collection untouched.
     *
     * @param array<string|UploadedFile> $files     New files to add (basename strings or UploadedFile instances).
     * @param array<int|string>          $deleteIds IDs of existing media items to remove.
     */
    public function syncMediaMultiple(
        HasMedia $model,
        string $collection,
        array $files = [],
        array $deleteIds = [],
    ): void {
        // Remove individually selected items
        if (!empty($deleteIds)) {
            $model->getMedia($collection)
                ->filter(fn ($media) => in_array($media->id, array_map('intval', $deleteIds)))
                ->each->delete();
        }

        // Append new files
        foreach ($files as $file) {
            if (empty($file)) {
                continue;
            }

            if (is_string($file) && !str_contains($file, DIRECTORY_SEPARATOR) && !str_contains($file, '/')) {
                $disk      = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default', 'local');
                $directory = config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp';
                $file      = Storage::disk($disk)->path($directory . '/' . $file);
            }

            $model->addMedia($file)->toMediaCollection($collection);
        }
    }
}
