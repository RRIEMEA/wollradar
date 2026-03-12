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
                                                    default => 'Ausstehend',
                                                }
                                            }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->created_at?->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-3 text-right">
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">E-Mail</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Admin</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @foreach ($approvedUsers as $u)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $u->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $u->is_admin ? 'Ja' : 'Nein' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex flex-col justify-end gap-2 sm:flex-row">
                                                @if (!$u->is_admin)
                                                    <form method="POST" action="{{ route('admin.users.makeAdmin', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="app-button w-full sm:w-auto">
                                                            Zum Admin machen
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.removeAdmin', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="app-button-secondary w-full sm:w-auto">
                                                            Admin entfernen
                                                        </button>
                                                    </form>
                                                @endif
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
        </div>
    </div>
</x-app-layout>
