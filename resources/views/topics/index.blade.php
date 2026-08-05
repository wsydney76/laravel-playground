@php
    use App\Models\Topic;
    use Illuminate\Support\Collection;
    /** @var Collection<Topic> $topics */
@endphp

<x-layouts::app :title="__('Topics')">
    @if ($topics->isEmpty())
        <p class="text-zinc-500">{{ __('No topics found.') }}</p>
    @else
        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3">
            @foreach ($topics as $topic)
                <x-topics.card :topic="$topic" />
            @endforeach
        </div>
    @endif
</x-layouts::app>

