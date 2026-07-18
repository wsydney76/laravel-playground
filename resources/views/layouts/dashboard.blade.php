<div class="flex w-full items-start">
    <div class="me-10 w-full pb-4 md:w-32">
        <flux:navlist aria-label="{{ __('Dashboard') }}">
            <flux:navlist.item :href="route('dashboard.articles')" wire:navigate>
                {{ __('Articles') }}
            </flux:navlist.item>
            @can('administer', App\Models\Article::class)
                <flux:navlist.item :href="route('dashboard.users')" wire:navigate>
                    {{ __('Users') }}
                </flux:navlist.item>
            @endcan
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>
