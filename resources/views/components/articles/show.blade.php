@props([
    /**@var\App\Models\Article*/'article',
])

<div {{ $attributes->class(['space-y-6']) }}>
    @if ($article->hasMedia('featured_image'))
        <img
            src="{{ $article->getFirstMediaUrl('featured_image', 'featured') }}"
            alt="{{ $article->title }}"
            class="w-full rounded-lg object-cover"
        />
    @endif

    <flux:card class="space-y-4">
        <p class="text-sm text-zinc-500">
            {{ __('By :name', ['name' => $article->creator->name]) }}
        </p>
        <p class="text-base text-zinc-800 dark:text-zinc-200">
            <x-nl2br :text="$article->body" />
        </p>
        <p class="mt-4 text-sm text-zinc-400">
            {{ $article->formatted_date_time }}
        </p>
    </flux:card>
</div>
