@props([
    'title' => 'Laravel Demos',
    'heading' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @if ($title)
            <title>{{ $title }} | {{ config('app.name') }}</title>
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

        <style>
            html {
                scrollbar-gutter: stable;
            }
        </style>
    </head>
    <body class="bg-bg antialiased dark:bg-zinc-900">
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

        <flux:main container>
            <x-layouts.status-callout />

            <div class="flex items-center justify-between">
                <flux:heading size="xl" class="my-6">{{ $heading ?? $title }}</flux:heading>
                <div id="titleslot"></div>
            </div>

            {{ $slot }}
        </flux:main>

        <flux:toast.group expanded position="top center">
            <flux:toast class="w-auto min-w-0 sm:min-w-96" />
        </flux:toast.group>
        @fluxScripts
    </body>
</html>
