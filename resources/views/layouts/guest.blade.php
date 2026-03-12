<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#fbbf24">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Wollradar') }}">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

        <title>{{ config('app.name', 'Wollradar') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="app-shell min-h-screen">
            @include('layouts.install-banner')
            <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8">
                <a href="/">
                    <x-application-logo class="h-20 w-20 fill-current text-amber-600" />
                </a>

                <div class="mt-6 w-full max-w-md rounded-[28px] border border-white/80 bg-white/90 px-6 py-5 shadow-[0_20px_60px_-30px_rgba(28,25,23,0.35)] backdrop-blur">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
