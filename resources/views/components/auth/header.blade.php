@props([
    'title',
    'description',
])

@php
    /** @var string $title */
    /** @var string $description */
@endphp

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
