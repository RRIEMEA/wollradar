<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Überblick</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">
                    Dashboard
                </h2>
                <p class="mt-2 max-w-2xl text-sm text-stone-600">
                    Die wichtigsten Bereiche sind für kleine Displays priorisiert: schneller erfassen, schneller finden, weniger horizontales Scrollen.
                </p>
            </div>

            <a href="{{ route('yarns.create') }}" class="app-button w-full sm:w-auto">
                Garn anlegen
            </a>
        </div>
    </x-slot>

    <div class="app-section">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($stats as $stat)
                <a href="{{ $stat['route'] }}" class="app-card transition hover:-translate-y-0.5 hover:shadow-[0_24px_60px_-34px_rgba(28,25,23,0.45)]">
                    <div class="text-sm font-medium text-stone-500">{{ $stat['label'] }}</div>
                    <div class="app-stat-value">{{ $stat['value'] }}</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">{{ $stat['hint'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-[1.4fr,1fr]">
            <div class="app-card">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-stone-950">Schnellzugriffe</h3>
                        <p class="mt-1 text-sm text-stone-500">Die wichtigsten Eingaben und Stammdaten direkt erreichbar.</p>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <a href="{{ route('yarns.create') }}" class="app-button">Neues Garn</a>
                    <a href="{{ route('yarns.index') }}" class="app-button-secondary">Garne öffnen</a>
                    <a href="{{ route('projects.index') }}" class="app-button-secondary">Projekte</a>
                    <a href="{{ route('colors.index') }}" class="app-button-secondary">Farben</a>
                    <a href="{{ route('materials.index') }}" class="app-button-secondary">Materialien</a>
                    <a href="{{ route('locations.index') }}" class="app-button-secondary">Orte</a>
                </div>
            </div>

            <div class="app-card">
                <h3 class="text-lg font-semibold text-stone-950">Status</h3>
                <div class="mt-4 space-y-3 text-sm text-stone-600">
                    <div class="rounded-2xl bg-stone-100 px-4 py-3">
                        <div class="font-medium text-stone-900">Angemeldet</div>
                        <div class="mt-1">{{ Auth::user()->email }}</div>
                    </div>

                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.users.pending') }}" class="block rounded-2xl bg-amber-50 px-4 py-3 transition hover:bg-amber-100">
                            <div class="font-medium text-stone-900">Ausstehende Registrierungen</div>
                            <div class="mt-1">{{ $pendingApprovals }} offene Freigaben</div>
                        </a>
                    @else
                        <div class="rounded-2xl bg-stone-100 px-4 py-3">
                            <div class="font-medium text-stone-900">Mobil optimiert</div>
                            <div class="mt-1">Listen, Buttons und Formulare sind jetzt auf Touch-Bedienung ausgelegt.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
