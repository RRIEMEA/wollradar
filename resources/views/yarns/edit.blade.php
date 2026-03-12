<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-amber-700">Eintrag bearbeiten</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Garn #{{ $yarn->id }} bearbeiten</h2>
            </div>
            <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">Zurück zu den Garnen</a>
        </div>
    </x-slot>

    <div class="app-section max-w-4xl">
        @if (session('status'))
            <div class="rounded-3xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
                @if(session('status') === 'Garn gespeichert.')
                    <span class="hidden" data-clear-draft-key="yarn-create"></span>
                @endif
                @if(session('status') === 'Garn aktualisiert.')
                    <span class="hidden" data-clear-draft-key="yarn-edit-{{ $yarn->id }}"></span>
                @endif
            </div>
        @endif

        <div class="app-card">
            <form method="POST" action="{{ route('yarns.update', $yarn) }}" enctype="multipart/form-data" class="space-y-8" data-draft-key="yarn-edit-{{ $yarn->id }}">
                @csrf
                @method('PUT')

                @include('yarns._form_fields', ['yarn' => $yarn])

                <x-form-draft-tools title="Yarn-Entwurf" hint="Anderungen an diesem Garn werden lokal zwischengespeichert." />

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('yarns.index') }}" class="app-button-secondary w-full sm:w-auto">Zurück</a>
                    <x-primary-button class="w-full sm:w-auto">
                        Aktualisieren
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @include('yarns._quick-add-modals')
</x-app-layout>
