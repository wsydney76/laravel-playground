@php
    use App\Models\Article;
@endphp

@if (Route::has('login'))
    @auth
        <flux:dropdown>
            <flux:button variant="subtle" aria-label="Preferred color scheme">
                <flux:avatar size="xs" :name="auth()->user()->name" color="auto" />
            </flux:button>
            <flux:menu>
                <flux:menu.group
                    :heading="auth()->user()->name . (auth()->user()->isAdmin() ? ' (Admin)' : '')"
                >
                    @can('create', App\Models\Article::class)
                        <flux:menu.item icon="plus-circle" href="{{ route('articles.create') }}">
                            {{ __('Add Article') }}
                        </flux:menu.item>
                    @endcan

                    @if (auth()->user()->isAdmin() ||auth()->user()->articles()->exists())
                        <flux:menu.item
                            :href="route('articles.my', app()->getLocale())"
                            icon="chat-bubble-left-ellipsis"
                            wire:navigate
                        >
                            {{ __('My Articles') }}
                        </flux:menu.item>

                        <flux:menu.separator />

                        <flux:menu.item
                            :href="route('dashboard.articles')"
                            icon="shield-check"
                            wire:navigate
                        >
                            {{ __('Admin Dashboard') }}
                        </flux:menu.item>
                    @endif

                    @can('administer', Article::class)
                        <flux:menu.item
                            :href="route('filament.admin.pages.dashboard')"
                            icon="wrench"
                            wire:navigate
                        >
                            {{ __('Filament') }}
                        </flux:menu.item>
                    @endcan

                    <flux:menu.separator />

                    <flux:menu.item :href="route('profile.edit')" icon="cog-8-tooth" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>

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
                </flux:menu.group>
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
