@props([
    'isAdmin' => false,
    'states' => [],
    'article',
])

@php
    use App\Enums\State;
    use App\Models\Article;
    /** @var bool $isAdmin */
    /** @var array<int, State> $states */
    /** @var Article $article */
@endphp

<flux:dropdown position="bottom" align="start" {{ $attributes }}>
    <flux:button
        icon="ellipsis-horizontal"
        variant="ghost"
        size="xs"
        inset="top bottom"
    ></flux:button>

    <flux:menu>
        <flux:menu.item
            icon="pencil-square"
            :href="route('articles.edit', ['article' => $article])"
        >
            {{ __('Edit') }}
        </flux:menu.item>

        @if ($isAdmin)
            <flux:menu.item icon="user" wire:click="openChangeOwner('{{ $article->id }}')">
                {{ __('Change owner') }}
            </flux:menu.item>
        @endif

        <flux:menu.item icon="arrow-top-right-on-square" :href="$article->url" target="_blank">
            {{ __('View on website') }}
        </flux:menu.item>

        <flux:menu.item
            icon="clock"
            wire:click="$dispatch('show-article-history', { id: {{ $article->id }} })"
        >
            {{ __('Show history') }}
        </flux:menu.item>

        <flux:menu.separator />

        @foreach ($this->states as $state)
            @if ($article->state !== $state)
                <flux:menu.item
                    :icon="$state->icon()"
                    wire:click="changeState('{{ $article->id }}', '{{ $state->value }}')"
                >
                    {{ $state->actionLabel() }}
                </flux:menu.item>
            @endif
        @endforeach

        <flux:menu.separator />

        <flux:menu.item
            icon="trash"
            variant="danger"
            wire:confirm="{{ __('Are you sure you want to delete this article?') }}"
            wire:click="destroyArticle('{{ $article->id }}')"
        >
            {{ __('Delete') }}
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
