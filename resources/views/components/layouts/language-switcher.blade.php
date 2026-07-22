<!-- Provisional, just replacing the language prefix -->

@use('App\Enums\Locale')

@php
    $currentLocale = app()->getLocale();
    $currentUrl = url()->current();
    $firstSegment = request()->segment(1);
@endphp

@if (Locale::tryFrom($firstSegment) !== null)
    <div class="flex items-center gap-2 pl-4">
        @foreach (Locale::cases() as $locale)
            @if ($locale->value === $currentLocale)
                <span
                    class="rounded py-1 text-xs font-semibold text-sky-700 uppercase dark:text-sky-300"
                    title="{{ $locale->label() }}"
                >
                    {{ $locale->shortLabel() }}
                </span>
            @else
                @php
                    $switchedUrl = preg_replace(
                        '#(^|/)' . preg_quote($currentLocale, '#') . '(/|$)#',
                        '$1' . $locale->value . '$2',
                        $currentUrl,
                    );
                    /** @var string|null $switchedUrl */
                @endphp

                <a
                    href="{{ $switchedUrl }}"
                    title="{{ $locale->label() }}"
                    class="rounded px-1 py-1 text-xs font-medium text-sky-500 uppercase hover:bg-sky-200 hover:text-sky-800 dark:text-sky-400 dark:hover:bg-sky-800 dark:hover:text-sky-200"
                >
                    {{ $locale->shortLabel() }}
                </a>
            @endif
        @endforeach
    </div>
@endif
