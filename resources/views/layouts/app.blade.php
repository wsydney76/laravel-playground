@props([
    'title' => config('app.name'),
    'titleactions' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scrollbar-gutter-stable">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ $title }} | {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fonts
        @fluxAppearance
    </head>
    <body class="bg-sky-50 antialiased dark:bg-sky-950">
        <flux:header
            container
            sticky
            class="border-b border-sky-300 bg-sky-100 dark:border-sky-700 dark:bg-sky-900"
        >
            <x-layouts.brand />

            <x-layouts.nav class="w-full max-lg:hidden" />

            <flux:spacer />

            <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />
        </flux:header>

        <flux:sidebar
            sticky
            collapsible="mobile"
            class="border-r border-sky-200 bg-sky-50 lg:hidden dark:border-sky-700 dark:bg-sky-900"
        >
            <flux:sidebar.header>
                <x-layouts.brand />
            </flux:sidebar.header>

            <x-layouts.sidebar-nav class="flex flex-col" />
        </flux:sidebar>

        <flux:main class="lg:mx-auto lg:w-5xl">
            <x-layouts.status-callout />

            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="my-6">{{ $title }}</flux:heading>
                @if ($titleactions && $titleactions->hasActualContent())
                    <div>
                        {{ $titleactions }}
                    </div>
                @endif
            </div>

            {{ $slot }}
        </flux:main>

        <flux:toast.group expanded position="top center">
            <flux:toast class="w-auto min-w-0 sm:min-w-96" />
        </flux:toast.group>
        @fluxScripts
    </body>
</html>
