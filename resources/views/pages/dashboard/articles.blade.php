<?php

use App\Enums\State;
use App\Models\Article;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Flux\Flux;

new #[Title('Dashboard - Articles')] class extends Component {
    use WithPagination;

    #[Url(as: 'user')]
    public string $filterUser = '';

    #[Url(as: 'state')]
    public string $filterState = '';

    #[Url(as: 'search')]
    public string $filterSearch = '';

    public function updating(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    #[Computed]
    public function states()
    {
        return State::cases();
    }

    #[Computed]
    public function isAdmin(): bool
    {
        return auth()->user()?->can('administer', Article::class) ?? false;
    }

    #[Computed]
    public function articles()
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        return Article::query()
            ->when(! $this->isAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->when($this->isAdmin && $this->filterUser, fn ($q) => $q->where('user_id', $this->filterUser))
            ->when($this->filterState, fn ($q) => $q->where('state', $this->filterState))
            ->when(
                $this->filterSearch,
                fn ($q) => $q->where('title', 'like', "%{$this->filterSearch}%"),
            )
            ->orderByDesc('created_at')
            ->paginate(8);
    }

    public function changeState(Article $article, $state): void
    {
        $this->authorize('update', $article);
        $article->state = $state;
        $article->save();

        Flux::toast(
            __('State changed to :state', ['state' => State::from($state)->label()]),
            variant: 'success',
        );
    }

    public function destroyArticle(Article $article)
    {
        $this->authorize('delete', $article);
        $article->delete();

        Flux::toast(__('Article deleted successfully'), variant: 'success');
    }

    public ?int $changeOwnerArticleId = null;
    public string $changeOwnerUserId = '';

    public function openChangeOwner(Article $article): void
    {
        $this->authorize('administer', Article::class);
        $this->changeOwnerArticleId = $article->id;
        $this->changeOwnerUserId = (string) $article->user_id;
        $this->modal('change-owner')->show();
    }

    public function applyChangeOwner(): void
    {
        $this->authorize('administer', Article::class);

        $article = Article::findOrFail($this->changeOwnerArticleId);
        $article->user_id = (int) $this->changeOwnerUserId;
        $article->save();

        $this->modal('change-owner')->close();
        $this->changeOwnerArticleId = null;
        $this->changeOwnerUserId = '';

        Flux::toast(__('Owner updated successfully'), variant: 'success');
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
    }
};
?>

<div>
    <x-layouts::dashboard
        :heading="__('Articles')"
        :subheading="$this->isAdmin ? __('Manage articles for all users and states') : __('Manage your articles')"
    >
        <div class="mb-4 flex gap-3">
            @if ($this->isAdmin)
                <flux:select wire:model.live="filterUser">
                    <flux:select.option option value="">
                        {{ __('All users') }}
                    </flux:select.option>
                    @foreach ($this->users as $user)
                        <flux:select.option value="{{ $user->id }}">
                            {{ $user->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:select wire:model.live="filterState">
                <flux:select.option value="">{{ __('All states') }}</flux:select.option>
                @foreach ($this->states as $state)
                    <flux:select.option value="{{ $state->value }}">
                        {{ $state->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                type="search"
                wire:model.live.debounce.300ms="filterSearch"
                :placeholder="__('Search by title')"
            />

            <flux:button
                class="mt-1"
                icon="x-circle"
                variant="ghost"
                square
                :disabled="(!$this->isAdmin || !$this->filterUser) && !$this->filterState && !$this->filterSearch"
                tooltip="{{ __('Reset filters') }}"
                size="sm"
                wire:click="resetFilters"
            ></flux:button>
        </div>

        @if ($this->articles->isEmpty())
            <flux:callout variant="warning">
                <flux:callout.heading>
                    {{ __('No articles found') }}
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('No articles were found for the selected filters.') }}
                </flux:callout.text>
            </flux:callout>
        @endif

        <flux:table :paginate="$this->articles">
            @foreach ($this->articles as $article)
                <flux:table.row wire:key="article-{{ $article->id }}">
                    <flux:table.cell>
                        <flux:link
                            as="button"
                            wire:click="$dispatch('show-article',{ id: {{ $article->id }} })"
                        >
                            {{ $article->title }}
                        </flux:link>

                        <flux:text size="sm" class="mt-2">
                            {{ $article->user->name }}
                            @if ($article->user->name !== $article->creator->name)
                                    ({{ __('Created by :name', ['name' => $article->creator->name]) }})
                            @endif

                            {{ $article->formattedDateTime }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            :color="$article->state->color()"
                            :icon="$article->state->icon()"
                        >
                            {{ $article->state->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown position="bottom" align="start">
                            <flux:button
                                icon="ellipsis-horizontal"
                                variant="ghost"
                                size="xs"
                                inset="top bottom"
                            ></flux:button>

                            <flux:menu>
                                <flux:menu.item
                                    icon="pencil-square"
                                    :href="route('articles.edit', ['article' => $article])"
                                >
                                    {{ __('Edit') }}
                                </flux:menu.item>

                                @if ($this->isAdmin)
                                    <flux:menu.item
                                        icon="user"
                                        wire:click="openChangeOwner('{{ $article->slug }}')"
                                    >
                                        {{ __('Change owner') }}
                                    </flux:menu.item>
                                @endif

                                <flux:menu.item
                                    icon="arrow-top-right-on-square"
                                    :href="route('articles.show', ['article' => $article])"
                                    target="_blank"
                                >
                                    {{ __('View on website') }}
                                </flux:menu.item>

                                <flux:menu.separator />

                                @foreach ($this->states as $state)
                                    @if ($article->state !== $state)
                                        <flux:menu.item
                                            :icon="$state->icon()"
                                            wire:click="changeState('{{ $article->slug }}', '{{ $state->value }}')"
                                        >
                                            {{ $state->actionLabel() }}
                                        </flux:menu.item>
                                    @endif
                                @endforeach

                                <flux:menu.separator />

                                <flux:menu.item
                                    icon="trash"
                                    variant="danger"
                                    wire:confirm="{{ __('Are you sure you want to delete this article?') }}"
                                    wire:click="destroyArticle('{{ $article->slug }}')"
                                >
                                    {{ __('Delete') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table>

        <flux:modal name="change-owner" class="min-w-88">
            <flux:heading size="lg">{{ __('Change owner') }}</flux:heading>
            <flux:subheading class="mt-1 mb-6">
                {{ __('Select a new owner for this article.') }}
            </flux:subheading>

            <flux:select wire:model="changeOwnerUserId" :label="__('Owner')">
                @foreach ($this->users as $user)
                    <flux:select.option value="{{ $user->id }}">
                        {{ $user->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="mt-6 flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="applyChangeOwner">
                    {{ __('Apply') }}
                </flux:button>
            </div>
        </flux:modal>

        <livewire:articles.details-modal />
    </x-layouts::dashboard>
</div>

<style>
    td {
        white-space: normal !important;
    }
    button {
        cursor: pointer;
    }
</style>
