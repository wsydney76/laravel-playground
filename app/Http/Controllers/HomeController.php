<?php

namespace App\Http\Controllers;

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

        // Only resolve a featured Article if there are Articles in the database
        // Queries inside of this conditional will therefore always find a Article,
        // no double-checking needed.
        if ($articlesCount) {
            // Cache an Article id for the current day
            $cacheKey = 'featured_article_id';
            $ttl = now()->tomorrow()->startOfDay();

            $featuredArticleId = Cache::remember($cacheKey, $ttl, function () {
                return Article::inRandomOrder()->value('id');
            });

            $featuredArticle = Article::find($featuredArticleId);

            // Featured Article obviously deleted, replace with a new one
            if ($featuredArticleId && !$featuredArticle) {
                $featuredArticle = Article::inRandomOrder()->first();
                Cache::put($cacheKey, $featuredArticle->id, $ttl);
            }

            // Don't show Article if user is not permitted to view
            if (!Gate::check('view', $featuredArticle)) {
                $featuredArticle = null;
            }
        }

        return view('home', compact(['page', 'articlesCount', 'featuredArticle']));
    }
}
