<x-layouts::app :title="__('Edit Article')">
    <x-articles.form
        :action="route('articles.update', $article)"
        method="PUT"
        :article="$article"
        :submit-label="__('Save Changes')"
        :cancel-href="$article->url"
    />
</x-layouts::app>
