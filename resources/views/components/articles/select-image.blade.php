<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    /**
     * @var Livewire\Features\SupportFileUploads\TemporaryUploadedFile $photo
     */

    #[Validate('image|max:10240')]
    public $photo;
    public string $label = 'Upload a photo';
    public ?string $filepath = null;

    public function removePhoto()
    {
        $this->photo->delete();
        $this->photo = null;
        $this->filepath = '';
    }

    public function updatedPhoto()
    {
        $this->filepath = $this->photo->getRealPath();
        // $this->dispatch('photo-updated', path: $this->photo->getRealPath());
    }

    public function getFilenameFromPath(string $path): string
    {
        $imageData = file_exists($path . '.json')
            ? json_decode(file_get_contents($path . '.json'), true)
            : [];

        return $imageData['name'] ?? '';
    }
};
?>

<div>
    <input id="file_path" type="hidden" name="filepath" value="{{ $filepath }}" />

    <div class="grid grid-cols-2 gap-4">
        <flux:file-upload wire:model="photo" :label="$label">
            <flux:file-upload.dropzone
                heading="Drop file here or click to browse"
                text="JPG, PNG, GIF up to 10MB"
                inline
                with-progress
            />
        </flux:file-upload>
        <div class="">
            @if ($filepath && ! $photo)
                <flux:label>Remembered image:</flux:label>

                {{ $this->getFilenameFromPath($filepath) }}
            @endif

            @if ($photo)
                <flux:label>Pending image:</flux:label>
                <flux:file-item
                    class="mt-2.5"
                    :heading="$photo->getClientOriginalName()"
                    :image="$photo->temporaryUrl()"
                    :size="$photo->getSize()"
                >
                    <x-slot name="actions">
                        <flux:file-item.remove
                            wire:click="removePhoto"
                            aria-label="{{ 'Remove file: ' . $photo->getClientOriginalName() }}"
                        />
                    </x-slot>
                </flux:file-item>
            @endif
        </div>
    </div>
</div>
