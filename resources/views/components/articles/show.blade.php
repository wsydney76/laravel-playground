@props([
    'article',
])

@php
    use App\Models\Article;
    /** @var Article $article */
@endphp

<div {{ $attributes->class(['space-y-6']) }}>
    @if ($url = $article->featured_image_url)
        <img
            src="{{ $url }}"
            alt="{{ $article->title }}"
            class="w-full rounded-lg object-cover"
        />
    @endif

    <flux:card class="space-y-4">
        <p class="text-sm text-zinc-500">
            {{ __('By :name', ['name' => $article->creator?->name ?? $article->user->name]) }}
        </p>
        <p class="text-base text-zinc-800 dark:text-zinc-200">
            <x-nl2br :text="$article->body" />
        </p>

        @if ($article->hasMedia('gallery'))
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                @foreach ($article->getMedia('gallery') as $image)
                    <a href="{{ $image->getUrl() }}" target="_blank" rel="noopener">
                        <img
                            src="{{ $image->getUrl('thumb') }}"
                            alt="{{ $image->file_name }}"
                            class="w-full rounded-lg object-cover"
                        />
                    </a>
                @endforeach
            </div>
        @endif

        <p class="mt-4 text-sm text-zinc-400">
            {{ $article->formatted_date_time }}
        </p>
    </flux:card>
</div>
