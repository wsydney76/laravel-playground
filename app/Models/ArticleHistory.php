<?php

namespace App\Models;

use App\Enums\ArticleAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleHistory extends Model
{
    protected $fillable = ['article_id', 'user_id', 'action', 'value', 'user_name'];

    protected function casts(): array
    {
        return [
            'action' => ArticleAction::class,
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
