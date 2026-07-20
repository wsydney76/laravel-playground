@props([
    'isAdmin' => false,
    'users' => [],
    'states' => [],
])

<div {{ $attributes->class(['mb-4 flex gap-3']) }}>
    @if ($isAdmin)
        <flux:select wire:model.live="filterUser">
            <flux:select.option option value="">
                {{ __('All users') }}
            </flux:select.option>
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">
                    {{ $user->name }}
                </flux:select.option>
            @endforeach
        </flux:select>
    @endif

    <flux:select wire:model.live="filterState">
        <flux:select.option value="">{{ __('All states') }}</flux:select.option>
        @foreach ($states as $state)
            <flux:select.option value="{{ $state->value }}">
                {{ $state->label() }}
            </flux:select.option>
        @endforeach

        <flux:select.option value="trashed">{{ __('Trashed') }}</flux:select.option>
    </flux:select>

    <flux:input
        type="search"
        wire:model.live.debounce.300ms="filterSearch"
        :placeholder="__('Search by title')"
    />

    <flux:select wire:model.live="perPage">
        <flux:select.option value="5">
            {{ __(':number per page', ['number' => 5]) }}
        </flux:select.option>
        <flux:select.option value="10">
            {{ __(':number per page', ['number' => 10]) }}
        </flux:select.option>
        <flux:select.option value="25">
            {{ __(':number per page', ['number' => 25]) }}
        </flux:select.option>
        <flux:select.option value="50">
            {{ __(':number per page', ['number' => 50]) }}
        </flux:select.option>
        <flux:select.option value="100">
            {{ __(':number per page', ['number' => 100]) }}
        </flux:select.option>
        <flux:select.option value="99999">{{ __('All on one page') }}</flux:select.option>
    </flux:select>

    <flux:button
        class="mt-1"
        icon="x-circle"
        variant="ghost"
        square
        x-bind:disabled="
            (! $wire.isAdmin || ! $wire.filterUser) &&
                ! $wire.filterState &&
                ! $wire.filterSearch
        "
        tooltip="{{ __('Reset filters') }}"
        size="sm"
        wire:click="resetFilters"
    ></flux:button>
</div>
