<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Inventory</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Yarns</h2>
                <p class="mt-2 max-w-2xl text-sm text-stone-600">
                    Mobile Ansicht mit Karten für schnellen Überblick, Desktop weiter mit Tabelle.
                </p>
            </div>

            <a href="{{ route('yarns.create') }}"
               class="app-button w-full sm:w-auto">
                Add Yarn
            </a>
        </div>
    </x-slot>

    <div class="app-section">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if($yarns->isEmpty())
            <div class="app-card-muted">
                <h3 class="text-lg font-semibold text-stone-900">No yarns yet.</h3>
                <p class="mt-2 text-sm text-stone-600">Lege den ersten Bestand an und ergänze bei Bedarf direkt ein Foto vom Smartphone.</p>
                <a href="{{ route('yarns.create') }}" class="app-button mt-4 w-full sm:w-auto">Add first yarn</a>
            </div>
        @else
            <div class="space-y-4 lg:hidden">
                @foreach($yarns as $yarn)
                    <article class="app-card">
                        <div class="flex items-start gap-4">
                            @if($yarn->photo_path)
                                <a href="{{ Storage::url($yarn->photo_path) }}" target="_blank" class="shrink-0">
                                    <img
                                        src="{{ Storage::url($yarn->photo_path) }}"
                                        alt="Yarn photo"
                                        class="h-20 w-20 rounded-2xl border border-stone-200 object-cover"
                                        loading="lazy"
                                    />
                                </a>
                            @else
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-stone-200 bg-stone-100 text-2xl text-stone-400">
                                    #
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-lg font-semibold text-stone-950">{{ $yarn->name ?? ('Yarn #' . $yarn->id) }}</div>
                                        <div class="mt-1 text-sm text-stone-500">#{{ $yarn->id }} · {{ $yarn->project?->name ?? 'No project' }}</div>
                                    </div>
                                    <span class="app-pill">{{ number_format((float) $yarn->quantity, 0, ',', '.') }} pcs</span>
                                </div>

                                <div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
                                    <div class="rounded-2xl bg-stone-100 px-3 py-2">
                                        <div class="text-stone-500">Length</div>
                                        <div class="mt-1 font-semibold text-stone-900">{{ $yarn->length_m === null ? '—' : number_format((float) $yarn->length_m, 0, ',', '.') . ' m' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-stone-100 px-3 py-2">
                                        <div class="text-stone-500">Weight</div>
                                        <div class="mt-1 font-semibold text-stone-900">{{ $yarn->weight_g === null ? '—' : number_format((float) $yarn->weight_g, 0, ',', '.') . ' g' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-stone-100 px-3 py-2">
                                        <div class="text-stone-500">Needles</div>
                                        <div class="mt-1 font-semibold text-stone-900">{{ $yarn->needle_size ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm text-stone-600">
                            <div>
                                <dt class="text-stone-400">Color</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->color?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Material</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->material?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Brand</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->brand?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Location</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->location?->name ?? '—' }}</dd>
                            </div>
                        </dl>

                        @if($yarn->notes)
                            <div class="mt-4 rounded-2xl bg-stone-50 px-4 py-3 text-sm leading-6 text-stone-600">
                                {{ $yarn->notes }}
                            </div>
                        @endif

                        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                            <a class="app-button-secondary w-full" href="{{ route('yarns.edit', $yarn) }}">
                                Edit
                            </a>

                            <form method="POST"
                                  action="{{ route('yarns.destroy', $yarn) }}"
                                  class="w-full"
                                  onsubmit="return confirm('Delete Yarn #{{ $yarn->id }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="app-button-danger w-full">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hidden lg:block app-card overflow-hidden !p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm table-fixed">
                        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Project</th>
                                <th class="px-4 py-3 text-left">Details</th>
                                <th class="px-4 py-3 text-right">Qty</th>
                                <th class="px-4 py-3 text-right">Length (m)</th>
                                <th class="px-4 py-3 text-right">Weight (g)</th>
                                <th class="px-4 py-3 text-left">Notes</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-stone-200 text-stone-800">
                            @foreach($yarns as $yarn)
                                <tr class="align-top hover:bg-stone-50">
                                    <td class="whitespace-nowrap px-4 py-4 text-stone-500">
                                        #{{ $yarn->id }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($yarn->photo_path)
                                                <a href="{{ Storage::url($yarn->photo_path) }}"
                                                   target="_blank"
                                                   class="shrink-0 inline-block">
                                                    <img
                                                        src="{{ Storage::url($yarn->photo_path) }}"
                                                        alt="Yarn photo"
                                                        class="h-14 w-14 rounded-2xl border border-stone-200 object-cover"
                                                        loading="lazy"
                                                    />
                                                </a>
                                            @else
                                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-stone-200 bg-stone-50 text-stone-400">
                                                    —
                                                </div>
                                            @endif

                                            <div class="min-w-0">
                                                <div class="truncate font-medium text-stone-900">
                                                    {{ $yarn->name ?? ('Yarn #' . $yarn->id) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="truncate font-medium text-stone-900">
                                            {{ $yarn->project?->name ?? '—' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="space-y-0.5 text-stone-700">
                                            <div><span class="text-stone-400">Color:</span> {{ $yarn->color?->name ?? '—' }}</div>
                                            <div><span class="text-stone-400">Material:</span> {{ $yarn->material?->name ?? '—' }}</div>
                                            <div><span class="text-stone-400">Brand:</span> {{ $yarn->brand?->name ?? '—' }}</div>
                                            <div><span class="text-stone-400">Location:</span> {{ $yarn->location?->name ?? '—' }}</div>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums">
                                        {{ number_format((float) $yarn->quantity, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums">
                                        {{ $yarn->length_m === null ? '—' : number_format((float) $yarn->length_m, 0, ',', '.') }}
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums">
                                        {{ $yarn->weight_g === null ? '—' : number_format((float) $yarn->weight_g, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="break-words whitespace-normal text-stone-600">
                                            {{ $yarn->notes ?? '' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex flex-col items-start gap-3">
                                            <a class="text-amber-700 hover:text-amber-800"
                                               href="{{ route('yarns.edit', $yarn) }}">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('yarns.destroy', $yarn) }}"
                                                  onsubmit="return confirm('Delete Yarn #{{ $yarn->id }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:text-red-800">
                                                    Delete
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

            <div class="app-card">
                {{ $yarns->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
