<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;

new class extends Component {
    use WithFileUploads;

    // ── Single-file mode ────────────────────────────────────────────────────
    public ?TemporaryUploadedFile $photo = null;
    public ?string $filepath = null;

    // ── Multiple-file mode ──────────────────────────────────────────────────
    /** @var TemporaryUploadedFile[] */
    public array $photos = [];
    /** @var string[] Basenames stored so old() can restore them after a failed submit */
    public array $filepaths = [];

    // ── Shared props ────────────────────────────────────────────────────────
    public bool $multiple = false;
    public ?string $label = null;
    public string $collection = 'default';
    public string $name = 'image';
    public string $thumbConversion = 'thumb';
    public string $accept = 'image/jpeg,image/png,image/webp';

    // ── Resolved from model in mount (single mode) ──────────────────────────
    public bool $hasMedia = false;
    public ?string $existingFileName = null;
    public ?string $existingFileUrl = null;

    // ── Resolved from model in mount (multiple mode) ────────────────────────
    /** @var array<array{id: int, name: string, url: string}> */
    public array $existingFiles = [];

    public function mount(
        ?HasMedia $model = null,
        bool $multiple = false,
        string $collection = 'default',
        string $name = 'image',
        ?string $label = null,
        string $thumbConversion = 'thumb',
        string $accept = 'image/jpeg,image/png,image/webp',
    ): void {
        $this->multiple = $multiple;
        $this->collection = $collection;
        $this->name = $name;
        $this->thumbConversion = $thumbConversion;
        $this->accept = $accept;
        $this->label = $label;

        if ($this->multiple) {
            // old() returns an array when the input was submitted as name[]
            $this->filepaths = old($name, []);

            if ($model?->hasMedia($collection)) {
                $this->hasMedia = true;
                foreach ($model->getMedia($collection) as $media) {
                    $this->existingFiles[] = [
                        'id'   => $media->id,
                        'name' => $media->file_name,
                        'url'  => $media->getUrl($thumbConversion),
                    ];
                }
            }
        } else {
            $this->filepath = old($name, '');

            if ($model?->hasMedia($collection)) {
                $this->hasMedia = true;
                $file = $model->getFirstMedia($collection);
                $this->existingFileName = $file?->file_name;
                $this->existingFileUrl = $file?->getUrl($thumbConversion);
            }
        }
    }

    // ── Single-file hooks ────────────────────────────────────────────────────

    public function removePhoto(int $index = -1): void
    {
        if ($this->multiple) {
            if (isset($this->photos[$index])) {
                $this->photos[$index]->delete();
                array_splice($this->photos, $index, 1);
                array_splice($this->filepaths, $index, 1);
            }
        } else {
            $this->photo->delete();
            $this->photo = null;
            $this->filepath = '';
        }
    }

    public function updatedPhoto(): void
    {
        if (! str_starts_with($this->photo->getMimeType(), 'image/')) {
            $this->photo->delete();
            $this->photo = null;

            return;
        }

        $this->filepath = basename($this->photo->getRealPath());
    }

    // ── Multiple-file hook ───────────────────────────────────────────────────

    public function updatedPhotos(): void
    {
        $valid      = [];
        $validPaths = [];

        foreach ($this->photos as $photo) {
            if (str_starts_with($photo->getMimeType(), 'image/')) {
                $valid[]      = $photo;
                $validPaths[] = basename($photo->getRealPath());
            } else {
                $photo->delete();
            }
        }

        $this->photos    = $valid;
        $this->filepaths = $validPaths;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function getFileDataFromPath(string $filename): array
    {
        $disk      = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default', 'local');
        $directory = config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp';
        $path      = \Illuminate\Support\Facades\Storage::disk($disk)->path($directory . '/' . $filename);

        $imageData = file_exists($path . '.json')
            ? json_decode(file_get_contents($path . '.json'), true)
            : [];

        $previewUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'livewire.preview-file',
            now()->addMinutes(30)->endOfHour(),
            ['filename' => $filename],
        );

        return [
            'name' => $imageData['name'] ?? $filename,
            'size' => $imageData['size'] ?? (file_exists($path) ? filesize($path) : null),
            'url'  => $previewUrl,
        ];
    }
};
?>

<div>
    @php
        $deleteName  = 'delete_' . $name;
        $deleteOld   = old($deleteName);
        $uploadLabel = $hasMedia ? __('Replace image') : __('Upload image');
    @endphp

    @if ($label)
        <flux:label>{{ $label }}</flux:label>
    @endif

    <flux:card size="sm" class="mt-2">

        {{-- ── Hidden inputs ──────────────────────────────────────────────── --}}
        @if ($multiple)
            @foreach ($filepaths as $fp)
                <input type="hidden" name="{{ $name }}[]" value="{{ $fp }}" />
            @endforeach
        @else
            <input id="file_path" type="hidden" name="{{ $name }}" value="{{ $filepath }}" />
        @endif

        <div class="grid grid-cols-2 gap-4">
            <flux:file-upload wire:model="{{ $multiple ? 'photos' : 'photo' }}" :label="$uploadLabel" :multiple="$multiple">
                <flux:file-upload.dropzone
                    heading="Drop file here or click to browse"
                    :text="$multiple ? 'JPG, PNG, WebP – select one or more' : 'JPG, PNG, GIF up to 10MB'"
                    inline
                    with-progress
                />
            </flux:file-upload>

            <div>
                @if ($multiple)
                    {{-- ── Multiple: restored from old() ──────────────────── --}}
                    @if (count($filepaths) && ! count($photos))
                        <flux:label>{{ __('Pending images:') }}</flux:label>
                        @foreach ($filepaths as $fp)
                            @php $remembered = $this->getFileDataFromPath($fp); @endphp
                            <flux:file-item
                                class="mt-2.5"
                                :heading="$remembered['name']"
                                :image="$remembered['url']"
                                :size="$remembered['size']"
                            />
                        @endforeach
                    @endif

                    {{-- ── Multiple: current session uploads ───────────────── --}}
                    @if (count($photos))
                        <flux:label>{{ __('Pending images:') }}</flux:label>
                        @foreach ($photos as $i => $p)
                            <flux:file-item
                                class="mt-2.5"
                                :heading="$p->getClientOriginalName()"
                                :image="$p->temporaryUrl()"
                                :size="$p->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove
                                        wire:click="removePhoto({{ $i }})"
                                        aria-label="{{ 'Remove file: ' . $p->getClientOriginalName() }}"
                                    />
                                </x-slot>
                            </flux:file-item>
                        @endforeach
                    @endif
                @else
                    {{-- ── Single: restored from old() ─────────────────────── --}}
                    @if ($filepath && ! $photo)
                        @php $rememberedFile = $this->getFileDataFromPath($filepath); @endphp
                        <flux:label>{{ __('Pending image:') }}</flux:label>
                        <flux:file-item
                            class="mt-2.5"
                            :heading="$rememberedFile['name']"
                            :image="$rememberedFile['url']"
                            :size="$rememberedFile['size']"
                        />
                    @endif

                    {{-- ── Single: current session upload ──────────────────── --}}
                    @if ($photo)
                        <flux:label>{{ __('Pending image:') }}</flux:label>
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
                @endif
            </div>
        </div>

        {{-- ── Existing media ─────────────────────────────────────────────── --}}
        @if ($multiple && count($existingFiles))
            <flux:label class="mt-4">{{ __('Current images') }}</flux:label>
            @foreach ($existingFiles as $file)
                <flux:file-item class="mt-4" :heading="$file['name']" :image="$file['url']">
                    <x-slot name="actions">
                        <div class="pt-1 pr-2">
                            <flux:checkbox
                                :label="__('Delete')"
                                :name="$deleteName . '[]'"
                                :value="$file['id']"
                                :checked="is_array($deleteOld) && in_array((string) $file['id'], array_map('strval', $deleteOld))"
                            />
                        </div>
                    </x-slot>
                </flux:file-item>
            @endforeach
        @elseif (! $multiple && $hasMedia)
            <flux:label class="mt-4">{{ __('Current image') }}</flux:label>
            <flux:file-item class="mt-4" :heading="$existingFileName" :image="$existingFileUrl">
                <x-slot name="actions">
                    <div class="pt-1 pr-2">
                        <flux:checkbox
                            :label="__('Delete')"
                            :name="$deleteName"
                            value="1"
                            :checked="(bool) $deleteOld"
                        />
                    </div>
                </x-slot>
            </flux:file-item>
        @endif

    </flux:card>
</div>
