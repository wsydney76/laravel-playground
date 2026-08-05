<?php

namespace App\Models;

use App\Enums\BadgeColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Topic extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'color' => BadgeColor::class,
        ];
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function getUrlAttribute(): string
    {
        return route('topics.show', [
            'locale' => app()->getLocale(),
            'topic'  => $this->id,
        ]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->acceptsMimeTypes([
            'image/jpeg',
            'image/png',
            'image/webp',
        ]);
    }

    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('featured')->fit(Fit::Crop, 1024, 350)->nonQueued();
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 300, 200)->nonQueued();
    }

    public function getFeaturedImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('featured_image', 'featured');
    }
}

