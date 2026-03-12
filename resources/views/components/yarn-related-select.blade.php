@props([
    'field',
    'label',
    'options' => collect(),
    'selected' => null,
    'modalName',
    'helper' => 'Fehlt der Eintrag? Du kannst ihn direkt hier neu anlegen.',
])

@php
    $selectedValue = old($field, $selected);
@endphp

<div x-data="{}">
    <div class="flex items-center justify-between gap-3">
        <label class="block text-sm font-medium text-stone-700">{{ $label }}</label>
        <button
            type="button"
            class="text-sm font-medium text-amber-800 underline decoration-amber-300 underline-offset-4 transition hover:text-amber-950"
            x-on:click="$dispatch('open-modal', '{{ $modalName }}')"
        >
            Neu anlegen
        </button>
    </div>

    <select
        name="{{ $field }}"
        class="mt-1 block w-full"
        x-on:change="if ($event.target.value === '__new__') { $event.target.value = ''; $dispatch('open-modal', '{{ $modalName }}') }"
    >
        <option value="">—</option>
        @foreach($options as $option)
            <option value="{{ $option->id }}" @selected((string) $selectedValue === (string) $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
        <option value="__new__">+ Neu anlegen …</option>
    </select>

    <p class="mt-1 text-xs text-stone-500">{{ $helper }}</p>

    @error($field)
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>
