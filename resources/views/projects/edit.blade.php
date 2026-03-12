<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Projekt bearbeiten</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Projekt bearbeiten</h2>
            </div>

            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                <a href="{{ route('yarns.create', ['project_id' => $project->id]) }}" class="app-button w-full sm:w-auto">
                    Garn für Projekt anlegen
                </a>
                <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">
                    Zurück zu den Projekten
                </a>
            </div>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Projekt aktualisiert.')
                    <span class="hidden" data-clear-draft-key="project-edit-{{ $project->id }}"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6" data-draft-key="project-edit-{{ $project->id }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-stone-700">Projektname</label>
                    <input
                        name="name"
                        value="{{ old('name', $project->name) }}"
                        class="mt-1 block w-full"
                        placeholder="z. B. Winterpullover 2026"
                        required
                    />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notizen (optional)</label>
                    <textarea
                        name="notes"
                        rows="4"
                        class="mt-1 block w-full"
                        placeholder="Optionale Notizen"
                    >{{ old('notes', $project->notes) }}</textarea>
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
                        @checked(old('is_finished', $project->is_finished))
                    />
                    <span>
                        <span class="block text-sm font-medium text-stone-900">Projekt fertig</span>
                        <span class="block text-sm text-stone-500">Zeigt an, dass dieses Projekt abgeschlossen ist.</span>
                    </span>
                </label>
                @error('is_finished')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror

                <x-form-draft-tools title="Projekt-Entwurf" hint="Änderungen an diesem Projekt bleiben lokal erhalten, bis die Aktualisierung gespeichert ist." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">
                        Zurück
                    </a>

                    <button type="submit" class="app-button w-full sm:w-auto">
                        Aktualisieren
                    </button>
                </div>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-3 text-sm font-medium text-stone-500">Gefahrenbereich</div>
            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                  onsubmit="return confirm('Projekt {{ $project->name }} wirklich löschen?')">
                @csrf
                @method('DELETE')
                <button class="app-button-danger w-full sm:w-auto">
                    Projekt löschen
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
