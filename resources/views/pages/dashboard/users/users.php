<?php


use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

new #[Title('Dashboard - Users')] class extends Component {
    use WithPagination;

    public function boot(ArticleService $articleService): void
    {
        $this->articleService = $articleService;
    }

    protected ArticleService $articleService;

    #[Url(as: 'role')]
    public string $filterRole = '';

    #[Url(as: 'verified')]
    public string $filterVerified = '';

    #[Url(as: 'search')]
    public string $filterSearch = '';

    public function mount(): void
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterVerified === '1', fn($q) => $q->whereNotNull('email_verified_at'))
            ->when($this->filterVerified === '0', fn($q) => $q->whereNull('email_verified_at'))
            ->when(
                $this->filterSearch,
                fn($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->filterSearch}%")->orWhere(
                        'email',
                        'like',
                        "%{$this->filterSearch}%",
                    );
                }),
            )
            ->withCount('articles')
            ->orderBy('name')
            ->paginate(15);
    }


    public function changeRole(User $user, string $role): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        if ($user->id === auth()->id() && $role !== 'admin') {
            Flux::toast(__('You cannot remove your own admin role.'), variant: 'danger');
            return;
        }

        $user->role = $role;
        $user->save();

        Flux::toast(__('Role updated to :role', ['role' => ucfirst($role)]), variant: 'success');
    }

    public function verifyEmail(User $user): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $user->email_verified_at = now();
        $user->save();

        Flux::toast(__('Email verified successfully'), variant: 'success');
    }

    public function deleteUser(User $user): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        if ($user->id === auth()->id()) {
            Flux::toast(__('You cannot delete your own account.'), variant: 'danger');
            return;
        }

        $articleCount = $user->articles()->count();

        if ($articleCount > 0) {
            Flux::toast(
                __(
                    'Cannot delete :name: they still own :count article(s). Reassign or delete their articles first.',
                    [
                        'name' => $user->name,
                        'count' => $articleCount,
                    ],
                ),
                variant: 'danger',
            );
            return;
        }

        $user->delete();

        Flux::toast(__('User deleted successfully'), variant: 'success');
    }

    public ?int $currentOwnerUserId = null;
    public string $changeOwnerUserId = '';

    public function openChangeOwner(User $user): void
    {
        $this->authorize('administer', Article::class);
        $this->currentOwnerUserId = $user->id;
        $this->dispatch(
            'select-user',
            heading: __('Reassign Articles'),
            subheading: __('Select a new owner for articles from this user.'),
            selectedUserId: $user->id,
            callbackEvent: 'apply-new-owner',
        );
    }

    #[On('apply-new-owner')]
    public function applyChangeOwner($id): void
    {
        $this->authorize('administer', Article::class);

        $currentOwner = User::findOrFail($this->currentOwnerUserId);
        $this->articleService->reassignArticles($currentOwner, (int) $id);

        $this->modal('change-owner')->close();
        $this->currentOwnerUserId = null;
        $this->changeOwnerUserId = '';

        Flux::toast(__('Owner updated successfully'), variant: 'success');
    }

    public function resetFilters(): void
    {
        $this->reset();
        $this->resetPage();
    }
};
