<x-layouts::app :title="__('New Article')">
    <x-articles.form
        :action="route('articles.store')"
        :submit-label="__('Publish')"
        :cancel-href="route('articles.index', app()->getLocale())"
    />
</x-layouts::app>
