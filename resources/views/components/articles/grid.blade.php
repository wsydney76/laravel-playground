@props([
    /**@var\Illuminate\Pagination\LengthAwarePaginator*/'articles',
])

@if ($articles->isEmpty())
    <p {{ $attributes->class(['text-zinc-500']) }}>{{ __('No articles found.') }}</p>
@else
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach ($articles as $article)
            <div>
                <x-articles.card :article="$article" />
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $articles->links() }}
    </div>
@endif
