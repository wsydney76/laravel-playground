<x-layouts::app :title="$article->title">
    <div class="space-y-6">
        @if ($article->hasMedia('featured_image'))
            <img
                src="{{ $article->getFirstMediaUrl('featured_image', 'featured') }}"
                alt="{{ $article->title }}"
                class="w-full rounded-lg object-cover"
            />
        @endif

        <flux:card class="space-y-4">
            <p class="text-sm text-zinc-500">
                {{ __('By :name', ['name' => $article->user->name]) }}
            </p>
            <p class="text-base text-zinc-800 dark:text-zinc-200">
                <x-nl2br :text="$article->body" />
            </p>
            <p class="mt-4 text-sm text-zinc-400">
                {{ $article->formatted_date_time }}
            </p>
        </flux:card>
    </div>

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
