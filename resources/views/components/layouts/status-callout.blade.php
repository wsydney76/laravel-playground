@session('status')
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
