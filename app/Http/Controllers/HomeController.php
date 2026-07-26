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

    /**
     * @return Article|null
     */
    protected function getFeaturedArticle(): ?Article
    {
        // Cache an Article id for the current day
        $cacheKey = 'featured_article_id';
        $ttl = now()->tomorrow()->startOfDay();

        $featuredArticleId = Cache::remember($cacheKey, $ttl, function () {
            return Articles::getFeaturedArticle()?->id;
        });

        if (!$featuredArticleId) {
            return null;
        }

        $featuredArticle = Article::find($featuredArticleId);

        // Featured Article obviously deleted, replace with a new one
        if (!$featuredArticle) {
            $featuredArticle = Articles::getFeaturedArticle();
            Cache::put($cacheKey, $featuredArticle?->id, $ttl);
        }

        // Don't show Article if user is not permitted to view
        if (!Gate::check('view', $featuredArticle)) {
            $featuredArticle = null;
        }
        return $featuredArticle;
    }
}
