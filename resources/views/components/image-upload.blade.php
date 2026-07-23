@props([
    'model' => null,
    'collection',
    'name',
    'label' => null,
    'thumbConversion' => 'thumb',
    'accept' => 'image/jpeg,image/png,image/webp',
])

@php
    use Spatie\MediaLibrary\HasMedia;
    /** @var HasMedia|null $model */
    /** @var string $collection */
    /** @var string $name */
    /** @var string|null $label */
    /** @var string $thumbConversion */
    /** @var string $accept */
    $hasMedia = $model?->hasMedia($collection);
    $deleteName = 'delete_' . $name;
    $deleteOld = old($deleteName);
@endphp

@if ($label)
    <flux:label>{{ $label }}</flux:label>
@endif

<flux:card size="sm" class="mt-2">
    <div class="flex items-start gap-8">
        {{-- File picker --}}
        <livewire:articles.select-image
            :label="$hasMedia ? __('Replace image') : __('Upload image')"
            :filepath="old('filepath', '')"
        />
    </div>

    @if ($hasMedia)
        {{-- Delete checkbox (only when an image already exists) --}}
        <flux:label class="mt-4">Current image</flux:label>
        @php($file = $model->getFirstMedia($collection))
        <flux:file-item
            class="mt-4"
            :heading="$file?->file_name"
            :image="$file?->getUrl($thumbConversion)"
        >
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
