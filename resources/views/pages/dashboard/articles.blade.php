<?php

use App\Enums\State;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Flux\Flux;

new #[Title('Dashboard - Articles')] class extends Component {
    use WithPagination;

    public function boot(ArticleService $articleService): void
    {
        $this->articleService = $articleService;
    }

    protected ArticleService $articleService;

    #[Url(as: 'user')]
    public string $filterUser = '';

    #[Url(as: 'state')]
    public string $filterState = '';

    #[Url(as: 'search')]
    public string $filterSearch = '';

    public array $selectedArticles = [];
    public bool $selectAll = false;

    public function updatingFilterUser(): void
    {
        $this->resetPage();
        $this->selectedArticles = [];
        $this->selectAll = false;
    }

    public function updatingFilterState(): void
    {
        $this->resetPage();
        $this->selectedArticles = [];
        $this->selectAll = false;
    }

    public function updatingFilterSearch(): void
    {
        $this->resetPage();
        $this->selectedArticles = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedArticles = $value
            ? $this->articles
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray()
            : [];
    }

    public function updatedSelectedArticles(): void
    {
        $this->selectAll =
            count($this->selectedArticles) > 0 &&
            count($this->selectedArticles) === $this->articles->count();
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
        return auth()
            ->user()
            ?->can('administer', Article::class) ?? false;
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
            ->when(
                $this->isAdmin && $this->filterUser,
                fn ($q) => $q->where('user_id', $this->filterUser),
            )
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
        $this->articleService->changeState($article, $state);

        Flux::toast(
            __('State changed to :state', ['state' => State::from($state)->label()]),
            variant: 'success',
        );
    }

    public function destroyArticle(Article $article)
    {
        $this->authorize('delete', $article);
        $this->articleService->delete($article);

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
        $this->articleService->changeOwner($article, (int) $this->changeOwnerUserId);

        $this->modal('change-owner')->close();
        $this->changeOwnerArticleId = null;
        $this->changeOwnerUserId = '';

        Flux::toast(__('Owner updated successfully'), variant: 'success');
    }

    // ── Bulk operations ──────────────────────────────────────────────────────

    public function bulkDelete(): void
    {
        $articles = Article::whereIn('id', $this->selectedArticles)->get();

        foreach ($articles as $article) {
            $this->authorize('delete', $article);
            $this->articleService->delete($article);
        }

        $count = $articles->count();
        $this->selectedArticles = [];
        $this->selectAll = false;

        Flux::toast(
            __(':count article(s) deleted successfully', ['count' => $count]),
            variant: 'success',
        );
    }

    public function bulkChangeState(string $state): void
    {
        $articles = Article::whereIn('id', $this->selectedArticles)->get();

        foreach ($articles as $article) {
            $this->authorize('update', $article);
            $this->articleService->changeState($article, $state);
        }

        $count = $articles->count();
        $this->selectedArticles = [];
        $this->selectAll = false;

        Flux::toast(
            __(':count article(s) changed to :state', [
                'count' => $count,
                'state' => State::from($state)->label(),
            ]),
            variant: 'success',
        );
    }

    public string $bulkChangeOwnerUserId = '';

    public function openBulkChangeOwner(): void
    {
        $this->authorize('administer', Article::class);
        $this->bulkChangeOwnerUserId = '';
        $this->modal('bulk-change-owner')->show();
    }

    public function applyBulkChangeOwner(): void
    {
        $this->authorize('administer', Article::class);

        $articles = Article::whereIn('id', $this->selectedArticles)->get();

        foreach ($articles as $article) {
            $this->articleService->changeOwner($article, (int) $this->bulkChangeOwnerUserId);
        }

        $count = $articles->count();

        $this->modal('bulk-change-owner')->close();
        $this->bulkChangeOwnerUserId = '';
        $this->selectedArticles = [];
        $this->selectAll = false;

        Flux::toast(
            __('Owner updated for :count article(s)', ['count' => $count]),
            variant: 'success',
        );
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
        {{-- Filters row --}}
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

        {{-- Bulk actions row --}}
        <x-dashboard.bulk-actions
            :is-admin="$this->isAdmin"
            :states="$this->states"
            :selected-articles="$this->selectedArticles"
        />

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
            <flux:table.columns>
                <flux:table.column class="w-8">
                    <flux:checkbox
                        wire:model.live="selectAll"
                        :indeterminate="count($this->selectedArticles) > 0 && !$this->selectAll"
                    />
                </flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('State') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            @foreach ($this->articles as $article)
                <flux:table.row wire:key="article-{{ $article->id }}">
                    <flux:table.cell class="w-8">
                        <flux:checkbox
                            wire:model.live="selectedArticles"
                            value="{{ $article->id }}"
                        />
                    </flux:table.cell>

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
                        <x-dashboard.row-menu
                            :is-admin="$this->isAdmin"
                            :states="$this->states"
                            :article="$article"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table>

        {{-- Single article change owner modal --}}
        <x-dashboard.select-owner
            :heading="__('Change owner')"
            :subheading="__('Select a new owner for this article.')"
            :users="$this->users"
        />

        {{-- Bulk change owner modal --}}
        <x-dashboard.bulk-select-owner :users="$this->users" />

        <livewire:articles.details-modal />
        <livewire:articles.history-modal />
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
