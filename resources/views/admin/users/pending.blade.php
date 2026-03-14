<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Admin</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Benutzerverwaltung</h2>
            </div>

            <a href="{{ route('dashboard') }}" class="app-button-secondary w-full sm:w-auto">
                Zurück zum Dashboard
            </a>
        </div>
    </x-slot>

    <div class="app-section max-w-5xl">

        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-5">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-stone-800">Ausstehende Registrierungen</h3>
                </div>

                @if ($pendingUsers->isEmpty())
                    <div class="app-card">
                        <p class="text-stone-700">Aktuell gibt es keine ausstehenden Registrierungen.</p>
                    </div>
                @else
                    <div class="app-card overflow-hidden !p-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">E-Mail</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Registriert am</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @foreach ($pendingUsers as $u)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $u->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{
                                                match ($u->status ?? 'PENDING') {
                                                    'APPROVED' => 'Freigegeben',
                                                    'REJECTED' => 'Abgelehnt',
                                                    'DEACTIVATED' => 'Deaktiviert',
                                                    default => 'Ausstehend',
                                                }
                                            }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->created_at?->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="mb-2 flex items-center gap-2 text-left text-xs text-stone-500 sm:justify-end sm:text-right">
                                                <span class="font-medium text-stone-700">Datenschutz</span>
                                                <label class="inline-flex items-center gap-2">
                                                    <input type="checkbox"
                                                           class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-0"
                                                           disabled
                                                           @checked((bool) $u->privacy_acknowledged_at)>
                                                    <span>{{ $u->privacy_acknowledged_at?->format('d.m.Y H:i') ?? 'nicht dokumentiert' }}</span>
                                                </label>
                                            </div>
                                            <div class="flex flex-col justify-end gap-2 sm:flex-row">
                                                <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="app-button w-full sm:w-auto">
                                                        Freigeben
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.users.reject', $u) }}"
                                                      onsubmit="return confirm('Diesen Benutzer wirklich ablehnen?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="app-button-secondary w-full sm:w-auto">
                                                        Ablehnen
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Freigegebene Benutzer --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-stone-800">Freigegebene Benutzer</h3>
                </div>

                @if (empty($approvedUsers) || $approvedUsers->isEmpty())
                    <div class="app-card">
                        <p class="text-stone-700">Es gibt noch keine freigegebenen Benutzer.</p>
                    </div>
                @else
                    <div class="app-card overflow-hidden !p-0">
                        <div class="divide-y divide-gray-200">
                            @foreach ($approvedUsers as $u)
                                <div class="grid gap-3 px-4 py-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)_auto] md:items-center">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $u->name }}</div>
                                        <div class="mt-1 text-sm text-gray-600">{{ $u->email }}</div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <span class="font-medium text-gray-900">Admin:</span> {{ $u->is_admin ? 'Ja' : 'Nein' }}
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="font-medium text-gray-900">Datenschutz:</span>
                                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox"
                                                       class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-0"
                                                       disabled
                                                       @checked((bool) $u->privacy_acknowledged_at)>
                                                <span>{{ $u->privacy_acknowledged_at?->format('d.m.Y H:i') ?? 'nicht dokumentiert' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap justify-start gap-2 md:justify-end">
                                        @if (!$u->is_admin)
                                            <form method="POST" action="{{ route('admin.users.makeAdmin', $u) }}">
                                                @csrf
                                                <button type="submit" class="app-button whitespace-nowrap">
                                                    Zum Admin machen
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.removeAdmin', $u) }}">
                                                @csrf
                                                <button type="submit" class="app-button-secondary whitespace-nowrap">
                                                    Admin entfernen
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.users.deactivate', $u) }}"
                                              onsubmit="return confirm('Diesen Benutzer wirklich deaktivieren?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="app-button-secondary whitespace-nowrap">
                                                Deaktivieren
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-stone-800">Abgelehnte Benutzer</h3>
                </div>

                @if ($rejectedUsers->isEmpty())
                    <div class="app-card">
                        <p class="text-stone-700">Es gibt aktuell keine abgelehnten Benutzer.</p>
                    </div>
                @else
                    <div class="app-card overflow-hidden !p-0">
                        <div class="divide-y divide-gray-200">
                            @foreach ($rejectedUsers as $u)
                                <div class="grid gap-3 px-4 py-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)_auto] md:items-center">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $u->name }}</div>
                                        <div class="mt-1 text-sm text-gray-600">{{ $u->email }}</div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <div><span class="font-medium text-gray-900">Status:</span> Abgelehnt</div>
                                        <div class="mt-1"><span class="font-medium text-gray-900">Registriert:</span> {{ $u->created_at?->format('d.m.Y H:i') }}</div>
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="font-medium text-gray-900">Datenschutz:</span>
                                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox"
                                                       class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-0"
                                                       disabled
                                                       @checked((bool) $u->privacy_acknowledged_at)>
                                                <span>{{ $u->privacy_acknowledged_at?->format('d.m.Y H:i') ?? 'nicht dokumentiert' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap justify-start gap-2 md:justify-end">
                                        <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="app-button whitespace-nowrap">
                                                Freigeben
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              onsubmit="return confirm('Diesen abgelehnten Benutzer wirklich endgültig löschen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-button-secondary whitespace-nowrap">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-stone-800">Deaktivierte Benutzer</h3>
                </div>

                @if ($deactivatedUsers->isEmpty())
                    <div class="app-card">
                        <p class="text-stone-700">Es gibt aktuell keine deaktivierten Benutzer.</p>
                    </div>
                @else
                    <div class="app-card overflow-hidden !p-0">
                        <div class="divide-y divide-gray-200">
                            @foreach ($deactivatedUsers as $u)
                                <div class="grid gap-3 px-4 py-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)_auto] md:items-center">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $u->name }}</div>
                                        <div class="mt-1 text-sm text-gray-600">{{ $u->email }}</div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <div><span class="font-medium text-gray-900">Status:</span> Deaktiviert</div>
                                        <div class="mt-1"><span class="font-medium text-gray-900">Registriert:</span> {{ $u->created_at?->format('d.m.Y H:i') }}</div>
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="font-medium text-gray-900">Datenschutz:</span>
                                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox"
                                                       class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-0"
                                                       disabled
                                                       @checked((bool) $u->privacy_acknowledged_at)>
                                                <span>{{ $u->privacy_acknowledged_at?->format('d.m.Y H:i') ?? 'nicht dokumentiert' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap justify-start gap-2 md:justify-end">
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              onsubmit="return confirm('Diesen deaktivierten Benutzer wirklich endgültig löschen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-button-secondary whitespace-nowrap">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
