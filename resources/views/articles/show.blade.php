@php
    use App\Models\Article;
    /** @var Article $article */
@endphp

<x-layouts::app :title="$article->title">
    @unless ($article->isPublished())
        <flux:callout class="mb-6" variant="danger" icon="exclamation-triangle">
            <flux:callout.heading>
                {{ __('This article is not published') }}
            </flux:callout.heading>
            <flux:callout.text>
                {{ __('Currently in the :state state.', ['state' => $article->state->label()]) }}
            </flux:callout.text>
        </flux:callout>
    @endunless

    <x-articles.show :article="$article" />

    <x-slot name="titleactions">
        <div class="flex gap-3">
            @can('update', $article)
                <flux:button
                    size="sm"
                    variant="primary"
                    color="sky"
                    as="a"
                    :href="route('articles.edit', $article)"
                >
                    {{ __('Edit') }}
                </flux:button>
            @endcan

            @can('delete', $article)
                <form
                    method="POST"
                    action="{{ route('articles.destroy', $article) }}"
                    onsubmit="return confirm('{{ __('Delete this article?') }}');"
                >
                    @csrf
                    @method('DELETE')
                    <flux:button size="sm" type="submit" variant="danger">
                        {{ __('Delete') }}
                    </flux:button>
                </form>
            @endcan
        </div>
    </x-slot>
</x-layouts::app>
