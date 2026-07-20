<div>
    <x-layouts::dashboard
        :heading="__('Articles')"
        :subheading="$this->isAdmin ? __('Manage articles for all users and states') : __('Manage your articles')"
    >
        {{-- Filters row --}}
        <x-dashboard.articles.filters
            :is-admin="$this->isAdmin"
            :users="$this->users"
            :states="$this->states"
        />

        {{-- Bulk actions row --}}
        @if ($this->filterState !== 'trashed')
            <x-dashboard.articles.bulk-actions
                :is-admin="$this->isAdmin"
                :states="$this->states"
                :selected-articles="$this->selectedArticles"
            />
        @endif

        @if ($this->articles->isEmpty())
            <flux:callout variant="warning">
                <flux:callout.heading>
                    {{ __('No articles found') }}
                </flux:callout.heading>
                <flux:callout.text>
                    {{ __('No articles were found for the selected filters.') }}
                </flux:callout.text>
            </flux:callout>
        @endif

        <flux:table :paginate="$this->articles">
            <flux:table.columns>
                <flux:table.column class="w-8">
                    <flux:checkbox
                        wire:model.live="selectAll"
                        :indeterminate="count($this->selectedArticles) > 0 && !$this->selectAll"
                    />
                </flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('State') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            @foreach ($this->articles as $article)
                <flux:table.row wire:key="article-{{ $article->id }}">
                    <flux:table.cell class="w-8">
                        <flux:checkbox
                            wire:model.live="selectedArticles"
                            value="{{ $article->id }}"
                        />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:link
                            as="button"
                            wire:click="$dispatch('show-article',{ id: {{ $article->id }} })"
                        >
                            {{ $article->title }}
                        </flux:link>

                        <flux:text size="sm" class="mt-2">
                            {{ $article->user->name }}
                            @if ($article->user->name !== $article->creator->name)
                                    ({{ __('Created by :name', ['name' => $article->creator->name]) }})
                            @endif

                            {{ $article->formattedDateTime }}
                        </flux:text>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                            :color="$article->state->color()"
                            :icon="$article->state->icon()"
                        >
                            {{ $article->state->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($this->filterState !== 'trashed')
                            <x-dashboard.articles.row-menu
                                :is-admin="$this->isAdmin"
                                :states="$this->states"
                                :article="$article"
                            />
                        @else
                            <flux:button
                                size="xs"
                                variant="primary"
                                color="red"
                                wire:click="restoreArticle({{ $article->id }})"
                                wire:confirm="{{ __('Are you sure you want to restore this article?') }}"
                            >
                                {{ __('Restore') }}
                            </flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table>

        <livewire:articles.details-modal />
        <livewire:articles.history-modal />
        <livewire:dashboard.shared.select-user />
    </x-layouts::dashboard>
</div>
