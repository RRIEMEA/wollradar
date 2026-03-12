@props([
    'yarn',
    'align' => 'right',
    'mobileSheet' => false,
])

@php
    $finishDisabled = ! $yarn->project_id || ($yarn->is_finished && $yarn->project?->is_finished);
@endphp

@if($mobileSheet)
    <div x-data="{ open: false }" class="relative">
        <button type="button" class="app-button-secondary-compact" @click="open = true">
            Aktionen
        </button>

        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-[120] lg:hidden"
        >
            <button
                type="button"
                class="absolute inset-0 bg-stone-950/35 backdrop-blur-sm"
                @click="open = false"
                aria-label="Aktionsmenü schließen"
            ></button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-4 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-4 opacity-0"
                class="absolute inset-x-4 bottom-4 rounded-[28px] border border-white/80 bg-white p-3 shadow-[0_24px_60px_-30px_rgba(28,25,23,0.45)]"
            >
                <div class="mb-2 flex items-center justify-between gap-3 px-2 pt-1">
                    <div class="text-sm font-semibold text-stone-900">Aktionen</div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl text-stone-400 transition hover:bg-stone-100 hover:text-stone-700"
                        @click="open = false"
                        aria-label="Schließen"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="2" stroke-linecap="round">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-1">
                    <form method="POST" action="{{ route('yarns.finish-project', $yarn) }}">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="block w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-950 disabled:cursor-not-allowed disabled:text-stone-400"
                            @disabled($finishDisabled)
                        >
                            Projekt fertig
                        </button>
                    </form>

                    <a
                        href="{{ route('yarns.edit', $yarn) }}"
                        class="block w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-950"
                    >
                        Bearbeiten
                    </a>

                    <form method="POST" action="{{ route('yarns.destroy', $yarn) }}" onsubmit="return confirm('Garn #{{ $yarn->id }} wirklich löschen?')">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="block w-full rounded-2xl px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                        >
                            Löschen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <x-dropdown :align="$align" width="48" contentClasses="py-2 bg-white">
        <x-slot name="trigger">
            <button type="button" class="app-button-secondary-compact">
                Aktionen
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-1 px-2">
                <form method="POST" action="{{ route('yarns.finish-project', $yarn) }}">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-950 disabled:cursor-not-allowed disabled:text-stone-400"
                        @disabled($finishDisabled)
                    >
                        Projekt fertig
                    </button>
                </form>

                <a
                    href="{{ route('yarns.edit', $yarn) }}"
                    class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-950"
                >
                    Bearbeiten
                </a>

                <form method="POST" action="{{ route('yarns.destroy', $yarn) }}" onsubmit="return confirm('Garn #{{ $yarn->id }} wirklich löschen?')">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                    >
                        Löschen
                    </button>
                </form>
            </div>
        </x-slot>
    </x-dropdown>
@endif
