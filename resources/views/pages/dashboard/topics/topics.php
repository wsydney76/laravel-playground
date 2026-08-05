<?php

use App\Concerns\HandlesMediaUpload;
use App\Models\Topic;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Flux\Flux;

new #[Title('Dashboard - Topics')] class extends Component {
    use WithFileUploads;
    use HandlesMediaUpload;

    // ── Create form ──────────────────────────────────────────────────────────
    public string $newTitle = '';
    public ?TemporaryUploadedFile $newImage = null;

    // ── Edit form ────────────────────────────────────────────────────────────
    public ?int $editingId = null;
    public string $editTitle = '';
    public ?TemporaryUploadedFile $editImage = null;
    public bool $deleteEditImage = false;

    public function mount(): void
    {
        $this->authorize('administer', \App\Models\Article::class);
    }

    #[Computed]
    public function topics()
    {
        return Topic::withCount('articles')->orderBy('title')->get();
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function createTopic(): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $validated = $this->validate(
            [
                'newTitle' => ['required', 'string', 'max:255', 'unique:topics,title'],
                'newImage' => ['nullable', 'image', 'max:10240'],
            ],
            [],
            [
                'newTitle' => __('Title'),
                'newImage' => __('Featured Image'),
            ],
        );

        $topic = Topic::create(['title' => $validated['newTitle']]);

        if ($this->newImage) {
            $this->syncMedia($topic, 'featured_image', $this->newImage->getRealPath());
        }

        $this->reset('newTitle', 'newImage');
        $this->modal('create-topic')->close();

        unset($this->topics);

        Flux::toast(__('Topic created successfully'), variant: 'success');
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function openEdit(Topic $topic): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $this->editingId = $topic->id;
        $this->editTitle = $topic->title;
        $this->editImage = null;
        $this->deleteEditImage = false;

        $this->modal('edit-topic')->show();
    }

    public function updateTopic(): void
    {
        $this->authorize('administer', \App\Models\Article::class);

        $topic = Topic::findOrFail($this->editingId);

        $validated = $this->validate(
            [
                'editTitle' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:topics,title,' . $this->editingId,
                ],
                'editImage' => ['nullable', 'image', 'max:10240'],
            ],
            [],
            [
                'editTitle' => __('Title'),
                'editImage' => __('Featured Image'),
            ],
        );

        $topic->update(['title' => $validated['editTitle']]);

        $this->syncMedia(
            $topic,
            'featured_image',
            $this->editImage?->getRealPath(),
            $this->deleteEditImage,
        );

        $this->reset('editingId', 'editTitle', 'editImage', 'deleteEditImage');
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
