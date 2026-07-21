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
        <div class="prose max-w-none md:col-span-3 dark:prose-invert">
            {!! nl2br(e($page->homepagetext)) !!}
        </div>
    </div>
</x-layouts::app>
