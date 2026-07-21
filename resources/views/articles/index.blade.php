@php
    use App\Models\Article;
@endphp

<x-layouts::app :title="$title">
    <x-slot name="titleactions">
        <flux:button
            size="sm"
            as="a"
            icon="magnifying-glass"
            href="{{ route('articles.search', app()->getLocale()) }}"
            variant="filled"
        >
            {{ __('Search') }}
        </flux:button>

        @can('create', Article::class)
            <flux:button
                size="sm"
                as="a"
                href="{{ route('articles.create') }}"
                variant="primary"
                color="sky"
                icon="plus"
            >
                {{ __('New Article') }}
            </flux:button>
        @endcan
    </x-slot>

    <x-articles.grid :articles="$articles" />
</x-layouts::app>
