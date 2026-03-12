<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Stammdaten</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Marken</h2>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Marke gespeichert.')
                    <span class="hidden" data-clear-draft-key="brand-create"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('brands.store') }}" class="space-y-4" data-draft-key="brand-create">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-stone-700">Neue Marke</label>
                    <input name="name" value="{{ old('name') }}"
                           class="mt-1 block w-full"
                           placeholder="z. B. Lana Grossa" />
                    @error('name')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <x-form-draft-tools title="Marken-Entwurf" hint="Neue Marken werden lokal zwischengespeichert." />

                <button class="app-button w-full sm:w-auto">Anlegen</button>
            </form>
        </div>

        <div class="app-card">
            <div class="mb-4 text-sm font-medium text-stone-500">Deine Marken</div>

            @if($brands->isEmpty())
                <div class="app-card-muted text-stone-600">Noch keine Marken vorhanden.</div>
            @else
                <ul class="space-y-3">
                    @foreach($brands as $brand)
                        <li class="flex flex-col gap-3 rounded-3xl border border-stone-200 bg-stone-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="font-medium text-stone-900">{{ $brand->name }}</div>
                            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                                <a href="{{ route('yarns.create', ['brand_id' => $brand->id]) }}"
                                   class="app-button-secondary w-full sm:w-auto">
                                    Garn anlegen
                                </a>
                                <form method="POST" action="{{ route('brands.destroy', $brand) }}"
                                      class="w-full sm:w-auto"
                                      onsubmit="return confirm('Marke {{ $brand->name }} wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="app-button-danger w-full sm:w-auto">
                                        Löschen
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
