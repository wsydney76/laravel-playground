<?php

namespace App\Services;

use App\Concerns\HandlesMediaUpload;
use App\Enums\ArticleAction;
use App\Enums\Locale;
use App\Models\Article;
use App\Models\ArticleHistory;
use App\Models\User;
use App\Notifications\ArticleCreated;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ArticleService
{
    use HandlesMediaUpload;
    public function resolveSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = $slug ? Str::slug($slug) : Str::slug($title);
        $candidate = $base;
        return $candidate;

        /*$i = 1;

        while (
            Article::where('slug', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i++;
        }

        return $candidate;*/
    }

    public function normalizeTranslatableFields(array $data, ?int $ignoreId = null): array
    {
        foreach (Locale::cases() as $locale) {
            $slug = $data['slug'][$locale->value] ?? '';
            $title = $data['title'][$locale->value] ?? '';

            if (empty($slug)) {
                $data['slug'][$locale->value] = $this->resolveSlug(null, $title, $ignoreId);
            }
        }

        return $data;
    }

    public function create(
        User $author,
        array $data,
        UploadedFile|string|null $featuredImage = null,
    ): Article {
        $data = $this->normalizeTranslatableFields($data);
        $data['creator_id'] = $author->id;

        $article = $author->articles()->create($data);

        $this->syncMedia($article, 'featured_image', $featuredImage);

        $this->recordHistory($article, $author, ArticleAction::Create, $article->title);

        $this->notifyAdmins($article);

        return $article;
    }

    public function update(
        Article $article,
        array $data,
        UploadedFile|string|null $featuredImage = null,
        bool $deleteFeaturedImage = false,
        array $galleryFiles = [],
        array $galleryDeleteIds = [],
    ): Article {
        $data = $this->normalizeTranslatableFields($data, $article->id);

        $article->update($data);

        $this->syncMedia($article, 'featured_image', $featuredImage, $deleteFeaturedImage);
        $this->syncMediaMultiple($article, 'gallery', $galleryFiles, $galleryDeleteIds);

        $this->recordHistory($article, auth()->user(), ArticleAction::Update, $article->title);

        return $article;
    }

    public function delete(Article $article): void
    {
        $this->recordHistory($article, auth()->user(), ArticleAction::Delete, $article->title);

        $article->delete();
    }

    public function changeState(Article $article, string $state): void
    {
        $article->state = $state;
        $article->save();

        $this->recordHistory($article, auth()->user(), ArticleAction::ChangeState, $state);
    }

    public function changeOwner(Article $article, int $newOwnerId): void
    {
        $newOwner = User::find($newOwnerId);

        $article->user_id = $newOwnerId;
        $article->save();

        $this->recordHistory(
            $article,
            auth()->user(),
            ArticleAction::ChangeOwner,
            $newOwner?->name ?? (string) $newOwnerId,
        );
    }

    public function reassignArticles(User $fromUser, int $toUserId): void
    {
        $toUser = User::find($toUserId);

        $fromUser->articles()->update(['user_id' => $toUserId]);

        $value = ($toUser?->name ?? (string) $toUserId) . ' (from ' . $fromUser->name . ')';

        foreach ($fromUser->articles()->withoutGlobalScopes()->get() as $article) {
            $this->recordHistory($article, auth()->user(), ArticleAction::ReassignArticles, $value);
        }
    }

    public function notifyAdmins(Article $article): void
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('id', '!=', $article->creator_id ?? auth()->id())
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new ArticleCreated($article));
        }
    }

    private function recordHistory(
        Article $article,
        ?User $actor,
        ArticleAction $action,
        ?string $value = null,
    ): void {
        ArticleHistory::create([
            'article_id' => $article->id,
            'user_id' => $actor?->id,
            'user_name' => $actor->name ?? '',
            'action' => $action->value,
            'value' => $value,
        ]);
    }
}
