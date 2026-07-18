<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticleCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('user')->published()->latest()->paginate(8);

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['title']);
        $data['creator_id'] = $request->user()->id;

        $article = $request->user()->articles()->create($data);

        if ($request->hasFile('featured_image')) {
            $article->addMediaFromRequest('featured_image')->toMediaCollection('featured_image');
        }

        $this->notifyAdmins($article);

        return $this->redirectToArticle($article, __('Article created successfully'));
    }

    public function show(Article $article): View
    {
        if (
            $article->state->value !== 'published' &&
            !auth()->user()?->can('viewUnpublished', $article)
        ) {
            abort(404);
        }

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('articles.edit', compact('article'));
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['title'], $article->id);

        $article->update($data);

        if ($request->hasFile('featured_image')) {
            $article->clearMediaCollection('featured_image');
            $article->addMediaFromRequest('featured_image')->toMediaCollection('featured_image');
        } elseif ($request->boolean('delete_featured_image')) {
            $article->clearMediaCollection('featured_image');
        }

        return $this->redirectToArticle($article, __('Article updated successfully'));
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $article->delete();

        return redirect()->route('articles.index')->with('status', __('Article deleted.'));
    }

    private function resolveSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = $slug ? Str::slug($slug) : Str::slug($title);
        $candidate = $base;
        $i = 1;

        while (
            Article::where('slug', $candidate)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i++;
        }

        return $candidate;
    }

    private function notifyAdmins(Article $article)
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('id', '!=', auth()->id())
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new ArticleCreated($article));
        }
    }

    /**
     * @param Article $article
     * @return RedirectResponse
     */
    protected function redirectToArticle(Article $article, $status): RedirectResponse
    {
        if ($article->state->value === 'published') {
            return redirect()
                ->route('articles.show', ['article' => $article])
                ->with('status', $status);
        }

        return redirect()->route('articles.index')->with('status', $status);
    }
}
