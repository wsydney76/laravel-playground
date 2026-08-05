@props([
    'topic',
])

@php
    use App\Models\Topic;
    /** @var Topic $topic */
@endphp

<flux:card {{ $attributes->class(['flex flex-col overflow-hidden p-0']) }}>
    @if ($url = $topic->featured_image_url)
        <a href="{{ $topic->url }}" class="block shrink-0 overflow-hidden">
            <img
                src="{{ $url }}"
                alt="{{ $topic->title }}"
                class="h-48 w-full object-cover transition-transform duration-300 hover:scale-102"
            />
        </a>
    @endif

    <div class="flex flex-1 flex-col gap-4 p-4">
        <a href="{{ $topic->url }}">
            <flux:badge color="{{ $topic->color }}">
                {{ $topic->title }}
            </flux:badge>
        </a>

        <flux:text class="text-sm text-zinc-500">
            {{ $topic->articles_count ?? $topic->articles->count() }} {{ __('articles') }}
        </flux:text>
    </div>
</flux:card>
