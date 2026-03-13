@props([
    'yarn',
    'compact' => false,
])

@php
    $quantity = (float) $yarn->quantity;
    $displayQuantity = fmod($quantity, 1.0) === 0.0
        ? number_format($quantity, 0, ',', '.')
        : number_format($quantity, 2, ',', '.');
    $wrapperClasses = $compact
        ? 'inline-flex items-center gap-1 rounded-full border border-stone-200 bg-white px-1 py-1'
        : 'inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-2 py-2 shadow-sm';
    $buttonClasses = $compact
        ? 'inline-flex h-8 w-8 items-center justify-center rounded-full border border-stone-200 text-base font-semibold text-stone-700 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-950'
        : 'inline-flex h-10 w-10 items-center justify-center rounded-full border border-stone-200 text-lg font-semibold text-stone-700 transition hover:border-stone-300 hover:bg-stone-50 hover:text-stone-950';
@endphp

<div class="{{ $wrapperClasses }}">
    <form method="POST" action="{{ route('yarns.quantity.adjust', $yarn) }}" data-preserve-scroll="true">
        @csrf
        @method('PATCH')
        <input type="hidden" name="direction" value="decrement" />
        <button type="submit" class="{{ $buttonClasses }}" aria-label="Menge verringern">
            −
        </button>
    </form>

    <div class="min-w-[4.5rem] px-1 text-center">
        <div class="text-xs uppercase tracking-[0.18em] text-stone-400">Qty</div>
        <div class="mt-0.5 text-sm font-semibold tabular-nums text-stone-900">{{ $displayQuantity }}</div>
    </div>

    <form method="POST" action="{{ route('yarns.quantity.adjust', $yarn) }}" data-preserve-scroll="true">
        @csrf
        @method('PATCH')
        <input type="hidden" name="direction" value="increment" />
        <button type="submit" class="{{ $buttonClasses }}" aria-label="Menge erhöhen">
            +
        </button>
    </form>
</div>
