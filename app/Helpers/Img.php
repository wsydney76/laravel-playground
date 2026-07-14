<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Exceptions\DriverException;
use Intervention\Image\Exceptions\ImageDecoderException;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\ImageManager;

class Img
{
    /**
     * @throws ImageDecoderException
     * @throws DriverException
     * @throws InvalidArgumentException
     */
    public static function url(?string $path, array $options = []): ?string
    {
        if (!$path) {
            return '';
        }

        $diskHandle = $options['disk'] ?? config('filesystems.default', 'public');
        $transformDiskHandle = $options['transformDisk'] ?? config('filesystems.default', 'public');

        $disk = Storage::disk($diskHandle);
        $transformDisk = Storage::disk($transformDiskHandle);

        // Cache bust based on ORIGINAL file's last modified time
        $timestamp = $disk->exists($path) ? $disk->lastModified($path) : time();

        $width = isset($options['width']) ? (int) $options['width'] : null;
        $height = isset($options['height']) ? (int) $options['height'] : null;
        $quality = isset($options['quality']) ? max(1, min(100, (int) $options['quality'])) : 85;

        // No resize requested -> return original URL (with cache bust)
        if (!$width) {
            return self::appendVersion($disk->url($path), $timestamp);
        }

        // Normalize format
        $originalExt = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $originalExt = $originalExt === 'jpeg' ? 'jpg' : $originalExt;

        $format = strtolower($options['format'] ?? $originalExt);
        if (!in_array($format, ['jpg', 'webp'], true)) {
            $format = 'jpg';
        }

        // Build transforms path while preserving directories:
        // blogimages/filename.jpg -> transforms/blogimages/filename_768x400.webp
        $dir = trim(pathinfo($path, PATHINFO_DIRNAME) ?? '', '.'); // '.' when no dir
        $filename = pathinfo($path, PATHINFO_FILENAME);

        $transformsRelativeDir = 'transforms/' . $diskHandle . ($dir ? '/' . $dir : '');
        $transformsPath = $transformsRelativeDir . "/{$filename}_{$width}x{$height}.{$format}";

        if (!$transformDisk->exists($transformsPath)) {
            // Ensure transforms directory exists
            $transformDisk->makeDirectory($transformsRelativeDir);

            $manager = new ImageManager(new Driver());

            /*if (!$transformDisk->exists($path)) {
                return null;
            }*/

            $image = $manager->decode($disk->get($path));

            if (!$height) {
                $height = ($image->height() / $image->width()) * $width;
            }

            // Exact size, center-cropped
            $image->cover($width, $height);

            $encoder = match ($format) {
                'webp' => new WebpEncoder(quality: $quality),
                default => new JpegEncoder(quality: $quality),
            };

            $transformDisk->put($transformsPath, (string) $image->encode($encoder));
        }

        return self::appendVersion($transformDisk->url($transformsPath), $timestamp);
    }

    private static function appendVersion(string $url, int $timestamp): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'v=' . $timestamp;
    }
}
