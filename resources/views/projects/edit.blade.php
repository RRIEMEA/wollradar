<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Update Project</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Edit Project</h2>
            </div>

            <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Project updated.')
                    <span class="hidden" data-clear-draft-key="project-edit-{{ $project->id }}"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6" data-draft-key="project-edit-{{ $project->id }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-stone-700">Project name</label>
                    <input
                        name="name"
                        value="{{ old('name', $project->name) }}"
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
                    >{{ old('notes', $project->notes) }}</textarea>
                    @error('notes')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <x-form-draft-tools title="Projekt-Entwurf" hint="Anderungen an diesem Projekt bleiben lokal erhalten, bis das Update gespeichert ist." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('projects.index') }}" class="app-button-secondary w-full sm:w-auto">
                        Back
                    </a>

                    <button type="submit" class="app-button w-full sm:w-auto">
                        Update
                    </button>
                </div>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-3 text-sm font-medium text-stone-500">Danger Zone</div>
            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                  onsubmit="return confirm('Delete {{ $project->name }}?')">
                @csrf
                @method('DELETE')
                <button class="app-button-danger w-full sm:w-auto">
                    Delete Project
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
