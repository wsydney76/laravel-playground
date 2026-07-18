@props([
    'isAdmin' => false,
    'states' => [],
    'selectedArticles' => [],
])

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
            <flux:menu.group :heading="__('Change state to')">
                @foreach ($states as $state)
                    <flux:menu.item
                        :icon="$state->icon()"
                        wire:click="bulkChangeState('{{ $state->value }}')"
                    >
                        {{ $state->actionLabel() }}
                    </flux:menu.item>
                @endforeach
            </flux:menu.group>

            @if ($isAdmin)
                <flux:menu.separator />
                <flux:menu.item icon="user" wire:click="openBulkChangeOwner">
                    {{ __('Change owner') }}
                </flux:menu.item>
            @endif

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
