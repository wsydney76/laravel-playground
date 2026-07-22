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
        <div>
            <flux:input
                :label="$hasMedia ? __('Replace image') : __('Upload image')"
                :id="$name"
                :name="$name"
                type="file"
                :accept="$accept"
            />
            @error($name)
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if ($hasMedia)
        {{-- Delete checkbox (only when an image already exists) --}}
        <flux:file-item
            class="mt-4"
            :heading="$model->getFirstMedia($collection)?->file_name"
            :image="$model->getFirstMediaUrl($collection, $thumbConversion)"
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
