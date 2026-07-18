@props([
    'this',
])

<flux:modal name="bulk-change-owner" {{ $attributes->class(['min-w-[22rem] space-y-6']) }}>
    <div>
        <flux:heading size="lg">{{ __('Change owner') }}</flux:heading>
        <flux:subheading>
            {{ __('Select a new owner for the selected articles.') }}
        </flux:subheading>
    </div>

    <flux:select wire:model="bulkChangeOwnerUserId" :placeholder="__('Select a user')">
        @foreach ($this->users as $user)
            <flux:select.option value="{{ $user->id }}">
                {{ $user->name }}
            </flux:select.option>
        @endforeach
    </flux:select>

    <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
        </flux:modal.close>
        <flux:button
            variant="primary"
            wire:click="applyBulkChangeOwner"
            x-bind:disabled="!$wire.bulkChangeOwnerUserId"
        >
            {{ __('Apply') }}
        </flux:button>
    </div>
</flux:modal>
