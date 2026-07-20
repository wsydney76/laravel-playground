<flux:brand
    :href="route('home', ['locale' => app()->getLocale()])"
    :name="config('app.name')"
    {{ $attributes }}
>
    <x-slot name="logo">
        <flux:icon icon="bolt" />
    </x-slot>
</flux:brand>
