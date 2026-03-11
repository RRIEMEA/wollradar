<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Meta Data</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Materials</h2>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Material saved.')
                    <span class="hidden" data-clear-draft-key="material-create"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('materials.store') }}" class="space-y-4" data-draft-key="material-create">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-stone-700">New Material</label>
                    <input name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full"
                           placeholder="e.g. Merino" />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <x-form-draft-tools title="Material-Entwurf" hint="Neue Materialien bleiben lokal erhalten, bis du sie speicherst." />

                <button class="app-button w-full sm:w-auto">Add</button>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-4 text-sm font-medium text-stone-500">Your Materials</div>

            @if($materials->isEmpty())
                <div class="app-card-muted text-stone-600">No materials yet.</div>
            @else
                <ul class="space-y-3">
                    @foreach($materials as $material)
                        <li class="flex flex-col gap-3 rounded-3xl border border-stone-200 bg-stone-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="font-medium text-stone-900">{{ $material->name }}</div>
                            <form method="POST" action="{{ route('materials.destroy', $material) }}"
                                  class="w-full sm:w-auto"
                                  onsubmit="return confirm('Delete {{ $material->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="app-button-danger w-full sm:w-auto">
                                    Delete
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
