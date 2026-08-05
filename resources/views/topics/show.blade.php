@php
    use App\Models\Topic;
    use App\Models\Article;
    use Illuminate\Pagination\LengthAwarePaginator;
    /** @var Topic $topic */
    /** @var LengthAwarePaginator<Article> $articles */
@endphp

<x-layouts::app :title="$topic->title">
    <div class="space-y-8">
        @if ($url = $topic->featured_image_url)
            <img
                src="{{ $url }}"
                alt="{{ $topic->title }}"
                class="w-full rounded-lg object-cover"
            />
        @endif

        <x-articles.grid :articles="$articles" />
    </div>
</x-layouts::app>

