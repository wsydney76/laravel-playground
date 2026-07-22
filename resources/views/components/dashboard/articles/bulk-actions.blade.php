@props([
    'isAdmin' => false,
    'states' => [],
    'selectedArticles' => [],
])

@php
    use App\Enums\State;
    /** @var bool $isAdmin */
    /** @var array<int, State> $states */
    /** @var array<int> $selectedArticles */
@endphp

<div {{ $attributes->class(['mb-3 flex items-center gap-3']) }}>
    <flux:dropdown>
        <flux:button
            icon-trailing="chevron-down"
            size="sm"
            variant="filled"
            :disabled="count($selectedArticles) === 0"
        >
            {{ __('Bulk actions') }}
        </flux:button>

        <flux:menu>
            @if ($isAdmin)
                <flux:menu.item icon="user" wire:click="openBulkChangeOwner">
                    {{ __('Change owner') }}
                </flux:menu.item>
            @endif

            <flux:menu.separator />

            @php
                $selectedStates = count($selectedArticles) > 0
                    ? \App\Models\Article::whereIn('id', $selectedArticles)->pluck('state')->unique()
                    : collect();
                $allSameState = $selectedStates->count() === 1 ? $selectedStates->first() : null;
                /** @var \Illuminate\Support\Collection<int, State> $selectedStates */
                /** @var State|null $allSameState */
            @endphp

            @foreach ($states as $state)
                @if ($allSameState === null || $state !== $allSameState)
                    <flux:menu.item
                        :icon="$state->icon()"
                        wire:click="bulkChangeState('{{ $state->value }}')"
                    >
                        {{ $state->actionLabel() }}
                    </flux:menu.item>
                @endif
            @endforeach

            <flux:menu.separator />

            <flux:menu.item
                icon="trash"
                variant="danger"
                wire:confirm="{{ __('Are you sure you want to delete the selected articles?') }}"
                wire:click="bulkDelete"
            >
                {{ __('Delete selected') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>

    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
        @if (count($selectedArticles) > 0)
            {{ __(':count selected', ['count' => count($selectedArticles)]) }}
        @else
            {{ __('No articles selected') }}
        @endif
    </flux:text>
</div>
