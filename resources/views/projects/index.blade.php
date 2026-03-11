<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Planning</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Projects</h2>
            <p class="mt-2 max-w-2xl text-sm text-stone-600">Projekte und Notizen bleiben auch auf kleinen Displays schnell bearbeitbar.</p>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Project saved.')
                    <span class="hidden" data-clear-draft-key="project-create"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4" data-draft-key="project-create">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-stone-700">Project name</label>
                    <input name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full"
                           placeholder="e.g. Winterpullover 2026" />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700">Notes (optional)</label>
                    <textarea name="notes" rows="4"
                              class="mt-1 block w-full"
                              placeholder="Optional notes">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <x-form-draft-tools title="Projekt-Entwurf" hint="Neue Projekte werden lokal zwischengespeichert, falls du unterbrochen wirst." />

                <button class="app-button w-full sm:w-auto">
                    Add Project
                </button>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-4 text-sm font-medium text-stone-500">Your Projects</div>

            @if($projects->isEmpty())
                <div class="app-card-muted">
                    <div class="text-stone-600">No projects yet.</div>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach($projects as $project)
                        <li class="rounded-3xl border border-stone-200 bg-stone-50/70 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="font-medium text-stone-900">{{ $project->name }}</div>
                                    @if($project->notes)
                                        <div class="mt-2 text-sm leading-6 text-stone-600">{{ $project->notes }}</div>
                                    @endif
                                </div>

                                <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:flex-row">
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="app-button-secondary w-full sm:w-auto">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                          class="w-full sm:w-auto"
                                          onsubmit="return confirm('Delete {{ $project->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="app-button-danger w-full sm:w-auto">
                                            Delete
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
