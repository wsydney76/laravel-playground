@php
    use App\Models\Homepage;
    /** @var Homepage $page */
    /** @var int $articlesCount */
@endphp

<x-layouts::app :title="$page->sitename">
    <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
        <div class="md:col-span-1">
            <div class="px-32 md:px-0">
                @if ($page->logo_src)
                    <img
                        src="{{ $page->logo_src }}"
                        alt="Homepage Image"
                        class="mb-4 h-auto w-full"
                    />
                @endif

                @if ($articlesCount)
                    <flux:button
                        as="a"
                        href="{{ route('articles.index', ['locale' => app()->getLocale()]) }}"
                        variant="primary"
                        class="w-full"
                    >
                        {{ __('View :count Articles', ['count' => $articlesCount]) }}
                    </flux:button>
                @endif
            </div>
        </div>
        <div class="space-y-8 md:col-span-3">
            <div class="prose max-w-none dark:prose-invert">
                {!! nl2br(e($page->homepagetext)) !!}
            </div>
            <div>
                <x-articles.featured-article :$featuredArticle />
            </div>
        </div>
    </div>
</x-layouts::app>
