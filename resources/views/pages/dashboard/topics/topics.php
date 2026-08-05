<?php

use App\Concerns\HandlesMediaUpload;
use App\Enums\BadgeColor;
use App\Models\Topic;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Flux\Flux;
use Illuminate\Support\Str;

new #[Title('Dashboard - Topics')] class extends Component {
    use WithFileUploads;
    use HandlesMediaUpload;

    // ── Create form ──────────────────────────────────────────────────────────
    public array $newTitle = ['en' => '', 'de' => ''];
    public array $newSlug  = ['en' => '', 'de' => ''];
    public string $newColor = 'sky';
    public ?TemporaryUploadedFile $newImage = null;

    // ── Edit form ────────────────────────────────────────────────────────────
    public ?int $editingId = null;
    public array $editTitle = ['en' => '', 'de' => ''];
    public array $editSlug  = ['en' => '', 'de' => ''];
    public string $editColor = 'sky';
    public ?TemporaryUploadedFile $editImage = null;
    public bool $deleteEditImage = false;

    public function mount(): void
    {
        $this->authorize('administer', \App\Models\Article::class);
    }

    #[Computed]
    public function topics()
    {
        return Topic::withCount('articles')->orderByRaw("JSON_EXTRACT(title, '$.en')")->get();
    }

    #[Computed]
    public function colors(): array
    {
        return BadgeColor::cases();
    }

    // ── Create ───────────────────────────────────────────────────────────────

    private function resolveSlug(?string $slug, string $title): string
    {
        return $slug ? Str::slug($slug) : Str::slug($title);
    }

    private function normalizeSlugs(array $title, array $slug): array
    {
        foreach (['en', 'de'] as $locale) {
            if (empty($slug[$locale])) {
                $slug[$locale] = $this->resolveSlug(null, $title[$locale] ?? '');
            } else {
                $slug[$locale] = Str::slug($slug[$locale]);
            }
        }
        return $slug;
    }

    public function createTopic(): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $validated = $this->validate([
            'newTitle.en' => ['required', 'string', 'max:255'],
            'newTitle.de' => ['nullable', 'string', 'max:255'],
            'newSlug.en'  => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'newSlug.de'  => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'newColor' => ['required', 'string', \Illuminate\Validation\Rule::enum(BadgeColor::class)],
            'newImage' => ['nullable', 'image', 'max:10240'],
        ], [], [
            'newTitle.en' => __('Title (EN)'),
            'newTitle.de' => __('Title (DE)'),
            'newSlug.en'  => __('Slug (EN)'),
            'newSlug.de'  => __('Slug (DE)'),
            'newColor' => __('Color'),
            'newImage' => __('Featured Image'),
        ]);

        $topic = Topic::create([
            'title' => $this->newTitle,
            'slug'  => $this->normalizeSlugs($this->newTitle, $this->newSlug),
            'color' => $validated['newColor'],
        ]);

        if ($this->newImage) {
            $this->syncMedia($topic, 'featured_image', $this->newImage->getRealPath());
        }

        $this->reset('newTitle', 'newImage');
        $this->newTitle = ['en' => '', 'de' => ''];
        $this->newSlug  = ['en' => '', 'de' => ''];
        $this->newColor = 'sky';
        $this->modal('create-topic')->close();

        unset($this->topics);

        Flux::toast(__('Topic created successfully'), variant: 'success');
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function openEdit(Topic $topic): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $this->editingId = $topic->id;
        $this->editTitle = $topic->getTranslations('title') + ['en' => '', 'de' => ''];
        $this->editSlug  = $topic->getTranslations('slug') + ['en' => '', 'de' => ''];
        $this->editColor = $topic->color->value;
        $this->editImage = null;
        $this->deleteEditImage = false;

        $this->modal('edit-topic')->show();
    }

    public function updateTopic(): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $topic = Topic::findOrFail($this->editingId);

        $validated = $this->validate([
            'editTitle.en' => ['required', 'string', 'max:255'],
            'editTitle.de' => ['nullable', 'string', 'max:255'],
            'editSlug.en'  => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'editSlug.de'  => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'editColor' => ['required', 'string', \Illuminate\Validation\Rule::enum(BadgeColor::class)],
            'editImage' => ['nullable', 'image', 'max:10240'],
        ], [], [
            'editTitle.en' => __('Title (EN)'),
            'editTitle.de' => __('Title (DE)'),
            'editSlug.en'  => __('Slug (EN)'),
            'editSlug.de'  => __('Slug (DE)'),
            'editColor' => __('Color'),
            'editImage' => __('Featured Image'),
        ]);

        $topic->update([
            'title' => $this->editTitle,
            'slug'  => $this->normalizeSlugs($this->editTitle, $this->editSlug),
            'color' => $validated['editColor'],
        ]);

        $this->syncMedia($topic, 'featured_image', $this->editImage?->getRealPath(), $this->deleteEditImage);

        $this->reset('editingId', 'editTitle', 'editImage', 'deleteEditImage');
        $this->editTitle = ['en' => '', 'de' => ''];
        $this->editSlug  = ['en' => '', 'de' => ''];
        $this->editColor = 'sky';
        $this->modal('edit-topic')->close();

        unset($this->topics);

        Flux::toast(__('Topic updated successfully'), variant: 'success');
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function deleteTopic(Topic $topic): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $topic->clearMediaCollection('featured_image');
        $topic->delete();

        unset($this->topics);

        Flux::toast(__('Topic deleted successfully'), variant: 'success');
    }
};
