<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Management
            </h2>

            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                Zurück zum Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Pending Registrations --}}
            <div class="mb-8">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Pending Registrations</h3>
                </div>

                @if ($pendingUsers->isEmpty())
                    <div class="rounded-md bg-white p-6 shadow">
                        <p class="text-gray-700">Aktuell gibt es keine ausstehenden Registrierungen.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-md bg-white shadow">
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
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $u->status ?? 'PENDING' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $u->created_at?->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                                    Freigeben
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.users.reject', $u) }}"
                                                  onsubmit="return confirm('Diesen User wirklich ablehnen?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
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
                @endif
            </div>

            {{-- Approved Users --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Approved Users</h3>
                </div>

                @if (empty($approvedUsers) || $approvedUsers->isEmpty())
                    <div class="rounded-md bg-white p-6 shadow">
                        <p class="text-gray-700">Es gibt noch keine freigegebenen User.</p>
                    </div>
                @else
                    <div class="overflow-hidden rounded-md bg-white shadow">
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
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $u->is_admin ? 'yes' : 'no' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if (!$u->is_admin)
                                                <form method="POST" action="{{ route('admin.users.makeAdmin', $u) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                                                        Make Admin
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.removeAdmin', $u) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                                                        Remove Admin
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
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
