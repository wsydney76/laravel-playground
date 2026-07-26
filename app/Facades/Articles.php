<?php

namespace App\Facades;

use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
/**
 * @method string resolveSlug(?string $slug, string $title, ?int $ignoreId = null)
 * @method array normalizeTranslatableFields(array $data, ?int $ignoreId = null)
 * @method Article create(User $author, array $data, UploadedFile|string|null $featuredImage = null)
 * @method Article update(Article $article, array $data, UploadedFile|string|null $featuredImage = null, bool $deleteFeaturedImage = false, array $galleryFiles = [], array $galleryDeleteIds = [], array $gallerySortedIds = [])
 * @method void delete(Article $article)
 * @method void changeState(Article $article, string $state)
 * @method void changeOwner(Article $article, int $newOwnerId)
 * @method void reassignArticles(User $fromUser, int $toUserId)
 * @method void notifyAdmins(Article $article)
 * @method Article|null getFeaturedArticle()
 */

class Articles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ArticleService::class;
    }
}
