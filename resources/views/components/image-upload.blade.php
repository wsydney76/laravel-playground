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

<flux:card class="mt-2">
    <div class="flex items-start gap-8">
        {{-- Current image preview --}}
        @if ($hasMedia)
            <div>
                <img
                    src="{{ $model->getFirstMediaUrl($collection, $thumbConversion) }}"
                    alt="{{ __('Current image') }}"
                    class="h-32 w-auto rounded-md object-cover"
                />
            </div>
        @endif

        {{-- File picker --}}
        <div>
            <flux:input :id="$name" :name="$name" type="file" :accept="$accept" />
            @error($name)
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Delete checkbox (only when an image already exists) --}}
        @if ($hasMedia)
            <div class="mt-2">
                <flux:checkbox
                    :label="__('Delete current image')"
                    :name="$deleteName"
                    value="1"
                    :checked="(bool) $deleteOld"
                />
                <p class="mt-2 text-xs text-zinc-400">
                    {{ __('Or upload a new image to replace it.') }}
                </p>
            </div>
        @endif
    </div>
</flux:card>
