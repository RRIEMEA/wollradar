<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">New Project</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Create Project</h2>
            </div>
            <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">Back to Projects</a>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        <div class="app-card">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4" data-draft-key="project-create">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-stone-700">Project name</label>
                    <input
                        name="name"
                        value="{{ old('name') }}"
                        class="mt-1 block w-full"
                        placeholder="e.g. Winterpullover 2026"
                        required
                    />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notes (optional)</label>
                    <textarea
                        name="notes"
                        rows="4"
                        class="mt-1 block w-full"
                        placeholder="Optional notes"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <x-form-draft-tools title="Projekt-Entwurf" hint="Name und Notizen bleiben lokal erhalten, bis du speicherst." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">Cancel</a>
                    <button type="submit" class="app-button w-full sm:w-auto">
                        Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
