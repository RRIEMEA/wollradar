<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Planung</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Projekte</h2>
            <p class="mt-2 max-w-2xl text-sm text-stone-600">Projekte und Notizen bleiben auch auf kleinen Displays schnell bearbeitbar.</p>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Projekt gespeichert.')
                    <span class="hidden" data-clear-draft-key="project-create"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4" data-draft-key="project-create">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-stone-700">Projektname</label>
                    <input name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full"
                           placeholder="z. B. Winterpullover 2026" />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notizen (optional)</label>
                    <textarea name="notes" rows="4"
                              class="mt-1 block w-full"
                              placeholder="Optionale Notizen">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <label class="flex items-center gap-3 rounded-3xl border border-stone-200 bg-stone-50/80 px-4 py-4">
                    <input
                        type="checkbox"
                        name="is_finished"
                        value="1"
                        class="h-5 w-5 rounded border-stone-300 text-amber-700 focus:ring-amber-500"
                        @checked(old('is_finished', false))
                    />
                    <span>
                        <span class="block text-sm font-medium text-stone-900">Projekt fertig</span>
                        <span class="block text-sm text-stone-500">Kann schon beim Anlegen gesetzt werden.</span>
                    </span>
                </label>
                @error('is_finished')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror

                <x-form-draft-tools title="Projekt-Entwurf" hint="Neue Projekte werden lokal zwischengespeichert, falls du unterbrochen wirst." />

                <button class="app-button w-full sm:w-auto">
                    Projekt anlegen
                </button>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-4 text-sm font-medium text-stone-500">Deine Projekte</div>

            @if($projects->isEmpty())
                <div class="app-card-muted">
                    <div class="text-stone-600">Noch keine Projekte vorhanden.</div>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach($projects as $project)
                        <li class="rounded-3xl border border-stone-200 bg-stone-50/70 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium text-stone-900">{{ $project->name }}</div>
                                    <label class="mt-3 inline-flex items-center gap-2 text-sm text-stone-600">
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-stone-300 text-amber-700 focus:ring-amber-500"
                                            disabled
                                            @checked($project->is_finished)
                                        />
                                        <span>Fertig</span>
                                    </label>
                                    @if($project->notes)
                                        <div class="mt-2 text-sm leading-6 text-stone-600">{{ $project->notes }}</div>
                                    @endif
                                </div>

                                <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
                                    <a href="{{ route('yarns.create', ['project_id' => $project->id]) }}"
                                       class="app-button-secondary w-full sm:w-auto">
                                        Garn anlegen
                                    </a>

                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="app-button-secondary w-full sm:w-auto">
                                        Bearbeiten
                                    </a>

                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                          class="w-full sm:w-auto"
                                          onsubmit="return confirm('Projekt {{ $project->name }} wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="app-button-danger w-full sm:w-auto">
                                            Löschen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
