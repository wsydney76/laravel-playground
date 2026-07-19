<?php

use App\Enums\State;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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

    #[Url]
    public int $perPage = 10;

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

    public function updatingPerPage(): void
    {
        $this->resetPage();
        $this->selectedArticles = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedArticles = $value
            ? $this->articles->pluck('id')->map(fn($id) => (string) $id)->toArray()
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
        return auth()->user()?->can('administer', Article::class) ?? false;
    }

    #[Computed]
    public function articles()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        return Article::query()
            ->when(!$this->isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->when(
                $this->isAdmin && $this->filterUser,
                fn($q) => $q->where('user_id', $this->filterUser),
            )
            ->when($this->filterState, fn($q) => $q->where('state', $this->filterState))
            ->when(
                $this->filterSearch,
                fn($q) => $q->where('title', 'like', "%{$this->filterSearch}%"),
            )
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
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

    public function openChangeOwner(Article $article): void
    {
        $this->authorize('administer', Article::class);
        $this->changeOwnerArticleId = $article->id;

        $this->dispatch(
            'select-user',
            heading: __('Change owner'),
            subheading: __('Select a new owner for this article.'),
            selectedUserId: $article->user_id,
            callbackEvent: 'apply-new-owner',
        );
    }

    #[On('apply-new-owner')]
    public function applyChangeOwner($id): void
    {
        $this->authorize('administer', Article::class);

        $article = Article::findOrFail($this->changeOwnerArticleId);
        $this->articleService->changeOwner($article, $id);

        $this->modal('change-owner')->close();
        $this->changeOwnerArticleId = null;

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

    public function openBulkChangeOwner(): void
    {
        $this->authorize('administer', Article::class);

        $this->dispatch(
            'select-user',
            heading: __('Change owner'),
            subheading: __('Select a new owner for the selected articles.'),
            callbackEvent: 'bulk-apply-new-owner',
        );
    }

    #[On('bulk-apply-new-owner')]
    public function applyBulkChangeOwner(int $id): void
    {
        $this->authorize('administer', Article::class);

        $articles = Article::whereIn('id', $this->selectedArticles)->get();

        foreach ($articles as $article) {
            $this->articleService->changeOwner($article, $id);
        }

        $count = $articles->count();

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
