@php
    use App\Helpers\Img;
@endphp

@props([
    'src',
    'width' => 1250,
    'height' => null,
    'format' => 'webp',
    'disk' => 'files',
    'transformDisk' => 'dist',
])

<img
    src="{{
        Img::url($src, [
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'disk' => $disk,
            'transformDisk' => $transformDisk,
        ])
    }}"
    {{ $attributes }}
/>
