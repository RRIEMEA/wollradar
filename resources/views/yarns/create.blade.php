<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Neuer Eintrag</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Garn anlegen</h2>
            </div>
            <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">Zurück zu den Garnen</a>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="mb-4 rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Garn gespeichert.')
                    <span class="hidden" data-clear-draft-key="yarn-create"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('yarns.store') }}" enctype="multipart/form-data" class="space-y-8" data-draft-key="yarn-create">
                @csrf

                @include('yarns._form')

                <x-form-draft-tools title="Yarn-Entwurf" hint="Neue Garn-Eintrage werden lokal zwischengespeichert, bis du sie speicherst." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">Abbrechen</a>
                    <button class="app-button w-full sm:w-auto">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    @include('yarns._quick-add-modals')
</x-app-layout>
