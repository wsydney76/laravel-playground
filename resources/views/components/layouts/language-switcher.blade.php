@use('App\Enums\Locale')

@php
    $currentLocale = app()->getLocale();
    $currentUrl = url()->current();
@endphp

<div class="pl-4 flex items-center gap-2">
    @foreach (Locale::cases() as $locale)
        @if ($locale->value === $currentLocale)
            <span class="rounded py-1 text-xs font-semibold uppercase text-sky-700 dark:text-sky-300" title="{{ $locale->label() }}">
                {{ $locale->shortLabel() }}
            </span>
        @else
            @php
                $switchedUrl = preg_replace(
                    '#(^|/)' . preg_quote($currentLocale, '#') . '(/|$)#',
                    '$1' . $locale->value . '$2',
                    $currentUrl,
                );
            @endphp
            <a
                href="{{ $switchedUrl }}"
                title="{{ $locale->label() }}"
                class="rounded py-1 text-xs font-medium uppercase text-sky-500 hover:bg-sky-200 hover:text-sky-800 dark:text-sky-400 dark:hover:bg-sky-800 dark:hover:text-sky-200"
            >
                {{ $locale->shortLabel() }}
            </a>
        @endif
    @endforeach
</div>

