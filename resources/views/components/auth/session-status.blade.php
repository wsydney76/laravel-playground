@php
    /** @var string $value */
@endphp
@session('status')
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $value }}
    </div>
@endsession
