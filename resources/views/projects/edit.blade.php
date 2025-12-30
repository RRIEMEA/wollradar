<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Project
            </h2>

            <a href="{{ route('projects.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded bg-green-50 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project name</label>
                        <input
                            name="name"
                            value="{{ old('name', $project->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g. Winterpullover 2026"
                            required
                        />
                        @error('name')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                        <textarea
                            name="notes"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional notes"
                        >{{ old('notes', $project->notes) }}</textarea>
                        @error('notes')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('projects.index') }}"
                            class="text-gray-700 hover:text-gray-900">
                            Back
                        </a>

                        <button type="submit"
                                class="px-4 py-2 rounded bg-indigo-600 text-gray-700 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-3">Danger Zone</div>
                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete {{ $project->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                        Delete Project
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
