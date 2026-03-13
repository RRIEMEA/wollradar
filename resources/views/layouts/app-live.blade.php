@php
    $primaryLinks = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['label' => 'Garne', 'route' => 'yarns.index', 'active' => request()->routeIs('yarns.*')],
        ['label' => 'Projekte', 'route' => 'projects.index', 'active' => request()->routeIs('projects.*')],
    ];

    $secondaryLinks = [
        ['label' => 'Farben', 'route' => 'colors.index', 'active' => request()->routeIs('colors.*')],
        ['label' => 'Materialien', 'route' => 'materials.index', 'active' => request()->routeIs('materials.*')],
        ['label' => 'Marken', 'route' => 'brands.index', 'active' => request()->routeIs('brands.*')],
        ['label' => 'Orte', 'route' => 'locations.index', 'active' => request()->routeIs('locations.*')],
    ];

    if (auth()->check() && auth()->user()->is_admin) {
        $secondaryLinks[] = [
            'label' => 'Freigaben',
            'route' => 'admin.users.pending',
            'active' => request()->routeIs('admin.users.*'),
        ];
    }

    $allLinks = array_merge($primaryLinks, $secondaryLinks);
@endphp
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
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

        <title>{{ config('app.name', 'Wollradar') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @include('layouts.vite-assets')
    </head>
    <body class="font-sans antialiased">
        <div class="app-shell">
            <div class="app-frame">
                <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-stone-200/80 bg-white/85 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="flex min-h-[4.5rem] items-center justify-between gap-3 py-3 lg:grid lg:grid-cols-[auto_minmax(0,1fr)_auto] lg:items-center lg:gap-4">
                            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center rounded-3xl p-1 transition hover:bg-amber-50 lg:mr-2 lg:border-r lg:border-stone-200/80 lg:pr-4">
                                <x-application-wordmark class="block h-9 w-auto max-w-[10.5rem] xl:h-10 xl:max-w-[12rem]" />
                            </a>

                            <div class="hidden lg:block lg:min-w-0">
                                <div class="flex min-w-0 flex-wrap items-center gap-2 px-1 pb-1">
                                    @foreach($primaryLinks as $link)
                                        <a href="{{ route($link['route']) }}"
                                           class="{{ $link['active'] ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }} whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>

                                @if(count($secondaryLinks) > 0)
                                    <div class="mt-2 flex min-w-0 flex-wrap items-center gap-2 px-1">
                                        @foreach($secondaryLinks as $link)
                                            <a href="{{ route($link['route']) }}"
                                               class="{{ $link['active'] ? 'border-stone-900 bg-stone-900 text-white shadow-sm' : 'border border-stone-200 bg-white text-stone-600 hover:bg-stone-100 hover:text-stone-900' }} whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold transition">
                                                {{ $link['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="hidden lg:flex lg:shrink-0 lg:items-center lg:gap-3 lg:border-l lg:border-stone-200/80 lg:pl-4">
                                <div x-data x-cloak
                                     class="inline-flex min-h-[34px] items-center rounded-full px-2.5 py-1.5 text-[11px] font-semibold"
                                     :class="$store.pwa && $store.pwa.online ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                                    <span class="mr-1.5 inline-block h-2 w-2 rounded-full"
                                          :class="$store.pwa && $store.pwa.online ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                    <span x-text="$store.pwa ? $store.pwa.statusLabel : 'Online'"></span>
                                </div>

                                <x-dropdown align="right" width="64" contentClasses="rounded-3xl border border-stone-200 bg-white/95 p-2 shadow-[0_18px_45px_-28px_rgba(28,25,23,0.35)] backdrop-blur">
                                    <x-slot name="trigger">
                                        <button type="button"
                                                class="inline-flex min-h-[36px] max-w-[11rem] items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 hover:text-stone-900">
                                            <span class="truncate">{{ Auth::user()->name }}</span>
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.512a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="space-y-1">
                                            <a href="{{ route('profile.edit') }}"
                                               class="block rounded-2xl px-4 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-900">
                                                Profil
                                            </a>

                                            <button
                                                x-data
                                                x-cloak
                                                x-show="$store.pwa && $store.pwa.installActionVisible"
                                                type="button"
                                                @click="$store.pwa.openInstallOptions()"
                                                class="block w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-900"
                                            >
                                                Als App speichern
                                            </button>

                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit"
                                                        class="block w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                                                    Abmelden
                                                </button>
                                            </form>
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            </div>

                            <button @click="open = !open"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-700 shadow-sm transition hover:bg-stone-50 lg:hidden"
                                    :aria-expanded="open.toString()"
                                    aria-label="Navigation umschalten">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div x-cloak x-show="open" x-transition.origin.top class="border-t border-stone-200/80 bg-stone-50/90 lg:hidden">
                        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($allLinks as $link)
                                    <a href="{{ route($link['route']) }}"
                                       class="{{ $link['active'] ? 'border-amber-300 bg-amber-100 text-amber-900' : 'border-stone-200 bg-white text-stone-700' }} rounded-2xl border px-4 py-3 text-sm font-medium shadow-sm transition">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>

                            <div class="app-card mt-4 space-y-3 !rounded-[24px] !p-4">
                                <div>
                                    <div class="text-sm font-semibold text-stone-900">{{ Auth::user()->name }}</div>
                                    <div class="text-sm text-stone-500">{{ Auth::user()->email }}</div>
                                </div>

                                <div x-data x-cloak
                                     class="inline-flex min-h-[36px] w-full items-center justify-center rounded-2xl px-3 py-2 text-sm font-semibold"
                                     :class="$store.pwa && $store.pwa.online ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                                    <span class="mr-2 inline-block h-2.5 w-2.5 rounded-full"
                                          :class="$store.pwa && $store.pwa.online ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                    <span x-text="$store.pwa && $store.pwa.online ? 'Online' : 'Offline'"></span>
                                </div>

                                <button
                                    x-data
                                    x-cloak
                                    x-show="$store.pwa && $store.pwa.installActionVisible"
                                    type="button"
                                    @click="$store.pwa.openInstallOptions(); open = false"
                                    class="app-button-secondary w-full"
                                >
                                    Als App speichern
                                </button>

                                <a href="{{ route('profile.edit') }}" class="app-button-secondary w-full">
                                    Profil
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="app-button-danger w-full">
                                        Abmelden
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>

                @include('layouts.install-banner')

                @isset($header)
                    <header class="app-header">
                        <div class="mx-auto w-full max-w-6xl">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="app-main">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
