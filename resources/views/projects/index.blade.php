<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 rounded bg-green-50 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project name</label>
                        <input name="name" value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. Winterpullover 2026" />
                        @error('name')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                        <textarea name="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Optional notes">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="px-4 py-2 rounded bg-indigo-600 text-gray-700 hover:bg-indigo-700">
                        Add
                    </button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-3">Your Projects</div>

                @if($projects->isEmpty())
                    <div class="text-gray-500">No projects yet.</div>
                @else
                    <ul class="divide-y">
                        @foreach($projects as $project)
                            <li class="py-3 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $project->name }}</div>
                                    @if($project->notes)
                                        <div class="text-sm text-gray-600 truncate">{{ $project->notes }}</div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <a href="{{ route('projects.edit', $project) }}"
                                    class="text-sm text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                        onsubmit="return confirm('Delete {{ $project->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </li>

                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
