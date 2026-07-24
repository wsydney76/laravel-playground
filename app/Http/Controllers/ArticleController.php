<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    public function index(): View
    {
        $articles = Article::with('user')->published()->latest()->paginate(8);
        $title = __('Articles');

        return view('articles.index', compact('articles', 'title'));
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $article = $this->articleService->create(
            $request->user(),
            $request->safe()->except('featured_image'),
            $request->featured_image,
        );

        return $this->redirectToArticle(
            $article,
            __('Article created successfully') . ' (' . $article->state->label() . ')',
        );
    }

    public function show(string $locale, Article $article, string $slug): View
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
        $this->articleService->update(
            $article,
            $request->safe()->except(['featured_image', 'delete_featured_image', 'gallery', 'delete_gallery', 'sort_gallery']),
            $request->featured_image,
            $request->boolean('delete_featured_image'),
            $request->input('gallery', []),
            $request->input('delete_gallery', []),
            $request->input('sort_gallery', []),
        );

        return $this->redirectToArticle(
            $article,
            __('Article updated successfully') . ' (' . $article->state->label() . ')',
        );
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $this->articleService->delete($article);

        return redirect()
            ->route('articles.index', ['locale' => app()->getLocale()])
            ->with('status', __('Article deleted.'));
    }

    protected function redirectToArticle(Article $article, string $status): RedirectResponse
    {
        if ($article->state->value === 'published') {
            return redirect()->to($article->url)->with('status', $status);
        }

        return redirect()
            ->route('articles.index', ['locale' => app()->getLocale()])
            ->with('status', $status);
    }

    public function my()
    {
        Gate::authorize('viewAny', Article::class);

        $articles = auth()->user()->articles()->latest()->paginate(8);
        $title = __('My Articles');
        return view('articles.index', compact('articles', 'title'));
    }
}
