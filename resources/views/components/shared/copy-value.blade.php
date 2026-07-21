@props([
    /**@var\mixed*/'locale',
    'id',
])
@use('App\Enums\Locale')
<div {{ $attributes->class(['mt-1']) }}>
    @foreach (Locale::cases() as $copyLocale)
        @if ($copyLocale !== $locale)
            <flux:button
                size="xs"
                variant="filled"
                color="sky"
                type="button"
                onclick="
                    document.getElementById('{{ $id }}-{{ $locale->value }}').value =
                        document.getElementById('{{ $id }}-{{ $copyLocale->value }}').value
                "
            >
                {{ __('Copy from :locale', ['locale' => $copyLocale->label()]) }}
            </flux:button>
        @endif
    @endforeach
</div>
