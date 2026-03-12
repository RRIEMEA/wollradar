<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Neues Projekt</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Projekt anlegen</h2>
            </div>
            <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">Zurück zu den Projekten</a>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        <div class="app-card">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4" data-draft-key="project-create">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-stone-700">Projektname</label>
                    <input
                        name="name"
                        value="{{ old('name') }}"
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
                    >{{ old('notes') }}</textarea>
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
                        <span class="block text-sm text-stone-500">Markiert das Projekt direkt als abgeschlossen.</span>
                    </span>
                </label>
                @error('is_finished')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror

                <x-form-draft-tools title="Projekt-Entwurf" hint="Name und Notizen bleiben lokal erhalten, bis du speicherst." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">Abbrechen</a>
                    <button type="submit" class="app-button w-full sm:w-auto">
                        Projekt speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
