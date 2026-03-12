<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Bestand</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Garne</h2>
                <p class="mt-2 max-w-2xl text-sm text-stone-600">
                    Mobile Ansicht mit Karten für schnellen Überblick, Desktop weiter mit Tabelle.
                </p>
            </div>

            <a href="{{ route('yarns.create') }}"
               class="app-button w-full sm:w-auto">
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

        <div class="app-card">
            <form method="GET" action="{{ route('yarns.index') }}" class="space-y-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Suche & Filter</div>
                        <p class="mt-2 text-sm text-stone-600">
                            {{ $hasFilters ? $yarns->total() . ' Treffer bei aktiven Filtern' : $totalYarnCount . ' Garne im Bestand' }}
                        </p>
                    </div>

                    @if($hasActiveControls)
                        <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">
                            Filter zurücksetzen
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div class="md:col-span-2 xl:col-span-3">
                        <label for="yarn-search" class="block text-sm font-medium text-stone-700">Suche</label>
                        <input
                            id="yarn-search"
                            type="search"
                            name="q"
                            value="{{ $filters['q'] }}"
                            class="mt-1 block w-full"
                            placeholder="Name, Projekt, Farbe, Charge, Notizen ..."
                        />
                    </div>

                    <div>
                        <label for="filter-project" class="block text-sm font-medium text-stone-700">Projekt</label>
                        <select id="filter-project" name="project_id" class="mt-1 block w-full">
                            <option value="">Alle Projekte</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected((string) $filters['project_id'] === (string) $project->id)>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-color" class="block text-sm font-medium text-stone-700">Farbe</label>
                        <select id="filter-color" name="color_id" class="mt-1 block w-full">
                            <option value="">Alle Farben</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" @selected((string) $filters['color_id'] === (string) $color->id)>
                                    {{ $color->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-material" class="block text-sm font-medium text-stone-700">Material</label>
                        <select id="filter-material" name="material_id" class="mt-1 block w-full">
                            <option value="">Alle Materialien</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" @selected((string) $filters['material_id'] === (string) $material->id)>
                                    {{ $material->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-brand" class="block text-sm font-medium text-stone-700">Marke</label>
                        <select id="filter-brand" name="brand_id" class="mt-1 block w-full">
                            <option value="">Alle Marken</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected((string) $filters['brand_id'] === (string) $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-location" class="block text-sm font-medium text-stone-700">Ort</label>
                        <select id="filter-location" name="location_id" class="mt-1 block w-full">
                            <option value="">Alle Orte</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-sort" class="block text-sm font-medium text-stone-700">Sortierung</label>
                        <select id="filter-sort" name="sort" class="mt-1 block w-full">
                            <option value="newest" @selected($filters['sort'] === 'newest')>Neueste zuerst</option>
                            <option value="updated_desc" @selected($filters['sort'] === 'updated_desc')>Zuletzt geändert</option>
                            <option value="oldest" @selected($filters['sort'] === 'oldest')>Älteste zuerst</option>
                            <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Name A–Z</option>
                            <option value="name_desc" @selected($filters['sort'] === 'name_desc')>Name Z–A</option>
                            <option value="quantity_desc" @selected($filters['sort'] === 'quantity_desc')>Bestand absteigend</option>
                            <option value="quantity_asc" @selected($filters['sort'] === 'quantity_asc')>Bestand aufsteigend</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    @if($activeFilterCount > 0)
                        <div class="flex items-center text-sm text-stone-500 sm:mr-auto">
                            {{ $activeFilterCount }} Filter aktiv
                        </div>
                    @endif

                    <button type="submit" class="app-button w-full sm:w-auto">
                        Ergebnisse anzeigen
                    </button>
                </div>
            </form>
        </div>

        @if($yarns->isEmpty() && !$hasFilters && $totalYarnCount === 0)
            <div class="app-card-muted">
                <h3 class="text-lg font-semibold text-stone-900">Noch keine Garne vorhanden.</h3>
                <p class="mt-2 text-sm text-stone-600">Lege den ersten Bestand an und ergänze bei Bedarf direkt ein Foto vom Smartphone.</p>
                <a href="{{ route('yarns.create') }}" class="app-button mt-4 w-full sm:w-auto">Erstes Garn anlegen</a>
            </div>
        @elseif($yarns->isEmpty())
            <div class="app-card-muted">
                <h3 class="text-lg font-semibold text-stone-900">Keine passenden Garne gefunden.</h3>
                <p class="mt-2 text-sm text-stone-600">Passe Suche oder Filter an, damit wieder Einträge angezeigt werden.</p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">Filter zurücksetzen</a>
                    <a href="{{ route('yarns.create') }}" class="app-button w-full sm:w-auto">Neues Garn anlegen</a>
                </div>
            </div>
        @else
            <div class="space-y-4 lg:hidden">
                @foreach($yarns as $yarn)
                    <article class="app-card relative z-0 !backdrop-blur-none focus-within:z-20">
                        <div class="flex items-start gap-4">
                            @if($yarn->photo_path)
                                <a href="{{ Storage::url($yarn->photo_path) }}" target="_blank" class="shrink-0">
                                    <img
                                        src="{{ Storage::url($yarn->photo_path) }}"
                                        alt="Garnfoto"
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
                                        <div class="text-lg font-semibold text-stone-950">{{ $yarn->name ?? ('Garn #' . $yarn->id) }}</div>
                                        <div class="mt-1 text-sm text-stone-500">{{ $yarn->project?->name ?? 'Kein Projekt' }}</div>
                                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-stone-600">
                                            <input
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500"
                                                disabled
                                                @checked($yarn->is_finished)
                                            />
                                            <span>Fertig</span>
                                        </label>
                                    </div>
                                    <x-yarn-quantity-stepper :yarn="$yarn" compact />
                                </div>
                            </div>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm text-stone-600">
                            <div>
                                <dt class="text-stone-400">Farbe</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->color?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Material</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->material?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Marke</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->brand?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Ort</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->location?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-stone-400">Nadeln</dt>
                                <dd class="mt-1 text-stone-900">{{ $yarn->needle_size ?? '—' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex justify-end">
                            <x-yarn-actions-menu :yarn="$yarn" mobile-sheet />
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hidden lg:block app-card overflow-hidden !p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm table-fixed">
                        <thead class="border-b border-stone-200 bg-stone-50 text-xs uppercase text-stone-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Projekt</th>
                                <th class="px-4 py-3 text-left">Details</th>
                                <th class="px-4 py-3 text-right">Menge</th>
                                <th class="px-4 py-3 text-left">Aktionen</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-stone-200 text-stone-800">
                            @foreach($yarns as $yarn)
                                <tr class="align-top hover:bg-stone-50">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($yarn->photo_path)
                                                <a href="{{ Storage::url($yarn->photo_path) }}"
                                                   target="_blank"
                                                   class="shrink-0 inline-block">
                                                    <img
                                                        src="{{ Storage::url($yarn->photo_path) }}"
                                                        alt="Garnfoto"
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
                                                    {{ $yarn->name ?? ('Garn #' . $yarn->id) }}
                                                </div>
                                                <label class="mt-2 inline-flex items-center gap-2 text-xs text-stone-600">
                                                    <input
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500"
                                                        disabled
                                                        @checked($yarn->is_finished)
                                                    />
                                                    <span>Fertig</span>
                                                </label>
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
                                                <div><span class="text-stone-400">Farbe:</span> {{ $yarn->color?->name ?? '—' }}</div>
                                                <div><span class="text-stone-400">Material:</span> {{ $yarn->material?->name ?? '—' }}</div>
                                                <div><span class="text-stone-400">Marke:</span> {{ $yarn->brand?->name ?? '—' }}</div>
                                                <div><span class="text-stone-400">Ort:</span> {{ $yarn->location?->name ?? '—' }}</div>
                                                <div><span class="text-stone-400">Nadeln:</span> {{ $yarn->needle_size ?? '—' }}</div>
                                            </div>
                                        </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right tabular-nums">
                                        <x-yarn-quantity-stepper :yarn="$yarn" />
                                    </td>

                                    <td class="px-4 py-4">
                                        <x-yarn-actions-menu :yarn="$yarn" align="right" />
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
