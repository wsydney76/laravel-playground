<flux:navbar {{ $attributes }}>
    {{--
        <flux:navbar.item icon="home" href="{{ route('home') }}" wire:navigate>
        Home
        </flux:navbar.item>
    --}}

    <x-layouts.nav-project />

    <flux:spacer />

    <div id="navslot"></div>

    <x-layouts.user />

    <x-layouts.nav-darkmode />
</flux:navbar>
