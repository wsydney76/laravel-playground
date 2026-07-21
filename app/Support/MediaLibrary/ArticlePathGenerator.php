<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class ArticlePathGenerator extends DefaultPathGenerator
{
    /**
     * Store conversions in the 'conversions' subfolder on the conversions disk.
     * Resulting path: conversions/{media_id}/
     * Run artisan media-library:regenerate to regenerate conversions after changing this path.
     */
    public function getPathForConversions(Media $media): string
    {
        return 'conversions/' . $this->getBasePath($media) . '/';
    }

    public function getPath(Media $media): string
    {
        return 'uploads/' . $this->getBasePath($media) . '/';
    }
}
