<?php

namespace App\Http\Controllers;

use App\Facades\Articles;
use App\Models\Article;
use App\Models\Homepage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{
    //
    public function show()
    {
        $page = Homepage::getSingleton();
        $articlesCount = Article::count();

        $featuredArticle = null;

        if ($articlesCount) {
            $featuredArticle = $this->getFeaturedArticle();
        }

        return view('home', compact(['page', 'articlesCount', 'featuredArticle']));
    }

    protected function getFeaturedArticle(): ?Article
    {
        $cacheKey = 'featured_article_id';
        $ttl = now()->endOfDay();

        $featuredArticleId = Cache::remember($cacheKey, $ttl, fn() => Articles::getFeaturedArticle()?->id);

        $featuredArticle = $featuredArticleId ? Article::find($featuredArticleId) : null;

        // Article was deleted — refresh the cache
        if ($featuredArticleId && !$featuredArticle) {
            $featuredArticle = Articles::getFeaturedArticle();
            Cache::put($cacheKey, $featuredArticle?->id, $ttl);
        }

        return $featuredArticle && Gate::check('view', $featuredArticle) ? $featuredArticle : null;
    }
}
