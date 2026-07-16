<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['user_id', 'title', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->timezone(config('app.app_timezone'))->isoFormat('LL');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->created_at->timezone(config('app.app_timezone'))->isoFormat('LLL');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->useDisk('local')
            ->storeConversionsOnDisk('dist')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('featured')->fit(Fit::Crop, 1024, 350)->nonQueued();
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 300, 200)->nonQueued();
    }
}
