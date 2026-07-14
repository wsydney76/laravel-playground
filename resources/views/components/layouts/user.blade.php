@if (Route::has('login'))
    @auth
        <flux:dropdown>
            <flux:button variant="subtle" icon="user" aria-label="Preferred color scheme">
                {{ \Illuminate\Support\Facades\Auth::user()->name }}
            </flux:button>
            <flux:menu>
                <flux:menu.item :href="route('profile.edit')" icon="cog-8-tooth" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full"
                        data-test="logout-button"
                    >
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    @else
        <flux:navbar.item :href="route('login')" icon="arrow-right-end-on-rectangle" wire:navigate>
            {{ __('Log in') }}
        </flux:navbar.item>

        <flux:navbar.item :href="route('register')" icon="user-plus" wire:navigate>
            {{ __('Register') }}
        </flux:navbar.item>
    @endauth
@endif
