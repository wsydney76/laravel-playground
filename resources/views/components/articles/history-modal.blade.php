<?php

use App\Enums\ArticleAction;
use App\Enums\State;
use App\Models\Article;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Article $article = null;

    #[On('show-article-history')]
    public function showHistory(int $id): void
    {
        $this->article = Article::with(['histories.user'])->findOrFail($id);
        $this->modal('article-history')->show();
    }
};
?>

<div>
    <flux:modal name="article-history" class="w-full max-w-3xl min-w-2xl">
        @if ($this->article)
            <flux:heading size="lg" class="mb-1">{{ $this->article->title }}</flux:heading>
            <flux:subheading class="mb-6">{{ __('Edit history') }}</flux:subheading>

            @if ($this->article->histories->isEmpty())
                <flux:callout variant="warning">
                    <flux:callout.text>
                        {{ __('No history found for this article.') }}
                    </flux:callout.text>
                </flux:callout>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Date') }}</flux:table.column>
                        <flux:table.column>{{ __('Action') }}</flux:table.column>
                        <flux:table.column>{{ __('Value') }}</flux:table.column>
                        <flux:table.column>{{ __('User') }}</flux:table.column>
                    </flux:table.columns>

                    @foreach ($this->article->histories as $history)
                        <flux:table.row wire:key="history-{{ $history->id }}">
                            <flux:table.cell
                                class="text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400"
                            >
                                {{ $history->created_at->timezone(config('app.app_timezone'))->isoFormat('LLL') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" variant="pill">
                                    {{ $history->action->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm">
                                @if ($history->action === ArticleAction::ChangeState && $history->value)
                                    @php
                                        $state = State::tryFrom($history->value);
                                    @endphp

                                    @if ($state)
                                        <flux:badge
                                            size="sm"
                                            :color="$state->color()"
                                            :icon="$state->icon()"
                                        >
                                            {{ $state->label() }}
                                        </flux:badge>
                                    @else
                                        {{ $history->value }}
                                    @endif
                                @else
                                    {{ $history->value ?? 'n/a' }}
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-sm">
                                {{ $history->user_name ?? 'n/a' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table>
            @endif

            <div class="mt-6 flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Close') }}</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </flux:modal>
</div>
