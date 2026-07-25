@if ($featuredArticle)
    <flux:card class="mt-8 space-y-4">
        <flux:heading size="lg">{{ __('Today\'s Featured Article') }}:</flux:heading>
        <flux:heading size="lg">{{ $featuredArticle->title }}</flux:heading>

        @if ($featuredArticle->body)
            <p>
                <x-nl2br :text="Str::limit($featuredArticle->body, 150, preserveWords: true)" />
            </p>

            <x-articles.meta :article="$featuredArticle" />

            <p>
                <flux:link :href="$featuredArticle->url">Read More</flux:link>
            </p>
        @endif
    </flux:card>
@endif
