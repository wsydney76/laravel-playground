<?php

namespace App\Facades;

use App\Services\ArticleService;
use Illuminate\Support\Facades\Facade;
/**
 * @method \App\Models\Article|null getFeaturedArticle()
 */

class Articles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ArticleService::class;
    }
}
