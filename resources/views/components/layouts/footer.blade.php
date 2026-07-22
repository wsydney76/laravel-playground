@props(['copyright' => ''])

@php
    /** @var string $copyright */
@endphp

<footer class="mt-8 border-t border-sky-200 py-4 dark:border-sky-700">
    <flux:text class="text-sm text-sky-600 dark:text-sky-400">
        &copy; {{ now()->year }} {{ $copyright }}
    </flux:text>
</footer>
