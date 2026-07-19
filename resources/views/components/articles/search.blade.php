<?php

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Computed]
    public function articles(): LengthAwarePaginator|Collection
    {
        if (! $this->search) {
            return collect();
        }

        return Article::query()
            ->where('title->' . app()->getLocale(), 'like', "%{$this->search}%")
            ->orWhere('body->' . app()->getLocale(), 'like', "%{$this->search}%")
            ->paginate(8);
    }

    public function render()
    {
        return $this->view()->title(__('Search Articles'));
    }
};
?>

<div class="space-y-6">
    <flux:input
        :label="__('Search for:')"
        type="search"
        wire:model.live.debounce.300ms="search"
        autofocus
        icon="magnifying-glass"
        :placeholder="__('Search in title and body...')"
    />

    @if ($search)
        <x-articles.grid :articles="$this->articles" />
    @else
        <p>
            {{ __('Type something to search for articles.') }}
        </p>
    @endif
</div>
