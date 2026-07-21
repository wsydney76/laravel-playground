<x-layouts::app :title="$page->sitename">
    <div class="prose dark:prose-invert max-w-none">
        {!! nl2br(e($page->homepagetext)) !!}
    </div>
</x-layouts::app>
