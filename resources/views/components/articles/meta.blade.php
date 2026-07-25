@props([
    'article',
])

<flux:text size="sm" {{ $attributes }}>
    <span>{{ $article->user->name }},</span>
    <span title="{{ $article->created_at->toFormattedDateString() }}">
        {{ $article->created_at->diffForHumans() }}
    </span>
</flux:text>
