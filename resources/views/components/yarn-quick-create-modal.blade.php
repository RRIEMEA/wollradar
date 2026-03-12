@props([
    'name',
    'title',
    'description',
    'action',
    'errorBag',
    'openKey',
    'nameField',
    'nameLabel' => 'Name',
    'namePlaceholder' => '',
    'notesField' => null,
    'notesLabel' => 'Notizen',
    'notesPlaceholder' => '',
    'submitLabel' => 'Speichern',
])

@php
    $bag = $errors->getBag($errorBag);
    $show = session('quick_add_open') === $openKey || $bag->any();
@endphp

<x-modal :name="$name" :show="$show" maxWidth="lg" focusable>
    <div class="bg-stone-50 px-6 py-6 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Schnell anlegen</p>
                <h3 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950">{{ $title }}</h3>
                <p class="mt-2 text-sm text-stone-600">{{ $description }}</p>
            </div>
            <button
                type="button"
                class="rounded-full border border-stone-200 px-3 py-2 text-sm font-medium text-stone-600 transition hover:border-stone-300 hover:text-stone-900"
                x-on:click="$dispatch('close-modal', '{{ $name }}')"
            >
                Schließen
            </button>
        </div>

        <form method="POST" action="{{ $action }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="quick_add" value="1" />
            <input type="hidden" name="redirect_to" value="{{ url()->full() . '#' . $openKey }}" />

            <div>
                <label for="{{ $nameField }}" class="block text-sm font-medium text-stone-700">{{ $nameLabel }}</label>
                <input
                    id="{{ $nameField }}"
                    name="{{ $nameField }}"
                    value="{{ old($nameField) }}"
                    class="mt-1 block w-full"
                    placeholder="{{ $namePlaceholder }}"
                />
                @if($bag->has($nameField))
                    <div class="mt-1 text-sm text-red-600">{{ $bag->first($nameField) }}</div>
                @endif
            </div>

            @if($notesField)
                <div>
                    <label for="{{ $notesField }}" class="block text-sm font-medium text-stone-700">{{ $notesLabel }}</label>
                    <textarea
                        id="{{ $notesField }}"
                        name="{{ $notesField }}"
                        rows="3"
                        class="mt-1 block w-full"
                        placeholder="{{ $notesPlaceholder }}"
                    >{{ old($notesField) }}</textarea>
                    @if($bag->has($notesField))
                        <div class="mt-1 text-sm text-red-600">{{ $bag->first($notesField) }}</div>
                    @endif
                </div>
            @endif

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    class="app-button-secondary w-full sm:w-auto"
                    x-on:click="$dispatch('close-modal', '{{ $name }}')"
                >
                    Abbrechen
                </button>
                <button type="submit" class="app-button w-full sm:w-auto">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</x-modal>
