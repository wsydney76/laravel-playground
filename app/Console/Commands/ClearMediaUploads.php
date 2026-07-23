<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearMediaUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clear-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all media library uploaded files and conversion images';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Clear uploads from the private local disk (storage/app/private/uploads)
        $this->clearDirectory(disk: 'local', path: 'uploads');

        // Clear conversions from the public dist disk (public/dist/conversions)
        $this->clearDirectory(disk: 'dist', path: 'conversions');

        // Clear Livewire temporary file uploads
        $livewireDisk = config('livewire.temporary_file_upload.disk') ?? config('filesystems.default');
        $livewireDirectory = config('livewire.temporary_file_upload.directory') ?? 'livewire-tmp';
        $this->clearDirectory(disk: $livewireDisk, path: $livewireDirectory);

        $this->info('Media uploads and conversions cleared.');
    }

    private function clearDirectory(string $disk, string $path): void
    {
        $storage = Storage::disk($disk);

        foreach ($storage->directories($path) as $directory) {
            $storage->deleteDirectory($directory);
        }

        foreach ($storage->files($path) as $file) {
            $storage->delete($file);
        }

        $this->line("Cleared {$disk}:/{$path}");
    }
}

