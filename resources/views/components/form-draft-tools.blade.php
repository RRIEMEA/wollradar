@props([
    'title' => 'Lokaler Entwurf',
    'hint' => 'Eingaben werden lokal im Browser zwischengespeichert.',
])

<div class="rounded-3xl border border-stone-200 bg-stone-50/90 px-4 py-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-stone-900">{{ $title }}</p>
            <p class="mt-1 text-sm leading-6 text-stone-600" data-draft-feedback>{{ $hint }}</p>
        </div>

        <button type="button" class="app-button-secondary w-full sm:w-auto" data-draft-clear>
            Entwurf loschen
        </button>
    </div>
</div>
