@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['label' => 'Yarns', 'route' => 'yarns.index', 'active' => request()->routeIs('yarns.*')],
        ['label' => 'Projects', 'route' => 'projects.index', 'active' => request()->routeIs('projects.*')],
        ['label' => 'Colors', 'route' => 'colors.index', 'active' => request()->routeIs('colors.*')],
        ['label' => 'Materials', 'route' => 'materials.index', 'active' => request()->routeIs('materials.*')],
        ['label' => 'Brands', 'route' => 'brands.index', 'active' => request()->routeIs('brands.*')],
        ['label' => 'Locations', 'route' => 'locations.index', 'active' => request()->routeIs('locations.*')],
    ];

    if (auth()->check() && auth()->user()->is_admin) {
        $links[] = [
            'label' => 'Approvals',
            'route' => 'admin.users.pending',
            'active' => request()->routeIs('admin.users.*'),
        ];
    }
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-stone-200/80 bg-white/85 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[4.5rem] items-center justify-between gap-3 py-3">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3 rounded-3xl p-1 transition hover:bg-amber-50">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <x-application-logo class="h-6 w-6 fill-current" />
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold text-stone-900">{{ config('app.name', 'Wollradar') }}</span>
                    <span class="block truncate text-xs text-stone-500">Bestand mobil verwalten</span>
                </span>
            </a>

            <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-between lg:gap-4">
                <div class="no-scrollbar flex min-w-0 items-center gap-2 overflow-x-auto pb-1">
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}"
                           class="{{ $link['active'] ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }} whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium transition">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center gap-3">
                    <div x-data x-cloak
                         class="inline-flex min-h-[36px] items-center rounded-full px-3 py-2 text-xs font-semibold"
                         :class="$store.pwa && $store.pwa.online ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                        <span class="mr-2 inline-block h-2.5 w-2.5 rounded-full"
                              :class="$store.pwa && $store.pwa.online ? 'bg-emerald-500' : 'bg-red-500'"></span>
                        <span x-text="$store.pwa ? $store.pwa.statusLabel : 'Online'"></span>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="app-button-secondary !min-h-0 !rounded-full !px-4 !py-2">
                        {{ Auth::user()->name }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="app-button-secondary !min-h-0 !rounded-full !px-4 !py-2">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <button @click="open = !open"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-700 shadow-sm transition hover:bg-stone-50 lg:hidden"
                    :aria-expanded="open.toString()"
                    aria-label="Toggle navigation">
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
                @foreach($links as $link)
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

                <a href="{{ route('profile.edit') }}" class="app-button-secondary w-full">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-button-danger w-full">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
