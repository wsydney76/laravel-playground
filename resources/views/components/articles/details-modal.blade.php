<?php

use App\Models\Article;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?Article $article = null;

    #[On('show-article')]
    public function showArticle(int $id): void
    {
        $this->article = Article::findOrFail($id);
        $this->modal('article-detail')->show();
    }
};
?>

<div>
    <flux:modal name="article-detail">
        @if ($this->article)
            <flux:heading class="mb-6 w-3/4" size="lg">{{ $this->article->title }}</flux:heading>

            <flux:button
                as="a"
                variant="filled"
                size="sm"
                icon:trailing="arrow-top-right-on-square"
                class="mb-6 block"
                :href="$this->article->url"
                target="_blank"
            >
                {{ __('View on website') }}
            </flux:button>

            <x-articles.show :article="$this->article" />
        @endif
    </flux:modal>
</div>
