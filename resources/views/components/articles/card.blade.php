@blaze

@props([
    'article',
])

<flux:card {{ $attributes->class(['flex flex-col overflow-hidden p-0']) }}>
    @if ($article->hasMedia('featured_image'))
        <a href="{{ route('articles.show', $article) }}" class="block shrink-0 overflow-hidden">
            <img
                src="{{ $article->getFirstMediaUrl('featured_image', 'featured') }}"
                alt="{{ $article->title }}"
                class="h-48 w-full object-cover transition-transform duration-300 hover:scale-102"
            />
        </a>
    @endif

    <div class="flex flex-1 flex-col gap-4 p-4">
        <a href="{{ route('articles.show', $article) }}">
            <flux:heading size="lg" class="hover:text-sky-700 hover:underline">
                {{ $article->title }}
            </flux:heading>
        </a>

        <flux:text>{{ Str::limit($article->body, 120, preserveWords: true) }}</flux:text>

        <div class="flex items-center justify-between">
            <flux:text size="sm">
                <span>{{ $article->user->name }},</span>
                <span title="{{ $article->created_at->toFormattedDateString() }}">
                    {{ $article->created_at->diffForHumans() }}
                </span>
            </flux:text>

            @can('update', $article)
                <flux:button
                    as="a"
                    href="{{ route('articles.edit', $article) }}"
                    variant="ghost"
                    size="sm"
                    icon="pencil-square"
                    title="{{ __('Edit') }}"
                />
            @endcan
        </div>
    </div>
</flux:card>
