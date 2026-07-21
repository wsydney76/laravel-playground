@props(['name' => config('app.name')])

<flux:brand
    :href="route('home', ['locale' => app()->getLocale()])"
    :name="$name"
    {{ $attributes }}
>
    <x-slot name="logo">
        <img src="{{ asset('files/logo.svg') }}" alt="{{ $name }}" />
    </x-slot>
</flux:brand>
