@session('status')
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => (show = false), 2000)"
        x-show="show"
        x-transition:leave="transition duration-1200 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <flux:callout
            :heading="__('Status')"
            variant="success"
            icon="information-circle"
            icon:variant="outline"
            class="mb-6"
        >
            <flux:callout.text>
                {{ session('status') }}
            </flux:callout.text>
        </flux:callout>
    </div>
@endsession

@if (request('verified') === '1')
    <flux:callout
        :heading="__('Status')"
        variant="success"
        icon="information-circle"
        icon:variant="outline"
        class="mb-6"
    >
        <flux:callout.text>Your E-mail address has been verified.</flux:callout.text>
    </flux:callout>
@endif
