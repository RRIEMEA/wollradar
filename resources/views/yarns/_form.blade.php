@php
    /** @var \App\Models\Yarn|null $yarn */
    $isEdit = isset($yarn);
    $currentPhotoUrl = !empty($yarn?->photo_path) ? Storage::url($yarn->photo_path) : null;
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

    <div class="md:col-span-2 xl:col-span-3">
        <div class="text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Basics</div>
        <p class="mt-2 text-sm text-stone-600">Die wichtigsten Angaben zuerst. Auf dem Handy sind alle Felder mit großen Touch-Zielen formatiert.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Project</label>
        <select name="project_id" class="mt-1 block w-full">
            <option value="">—</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(old('project_id', $yarn->project_id ?? null) == $p->id)>
                    {{ $p->name }}
                </option>
            @endforeach
        </select>
        @error('project_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-1 xl:col-span-2">
        <label class="block text-sm font-medium text-stone-700">Yarn name</label>
        <input name="name"
            value="{{ old('name', $yarn->name ?? '') }}"
            class="mt-1 block w-full"
            placeholder="e.g. Weihnachtssocken (Merino Gelb)"/>
        @error('name')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-stone-700">Color code</label>
            <input name="color_code"
                value="{{ old('color_code', $yarn->color_code ?? '') }}"
                class="mt-1 block w-full"
                placeholder="e.g. 401 / A12"/>
            @error('color_code')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Batch number</label>
            <input name="batch_number"
                value="{{ old('batch_number', $yarn->batch_number ?? '') }}"
                class="mt-1 block w-full"
                placeholder="Dye Lot / Charge"/>
            @error('batch_number')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Needle size</label>
            <input name="needle_size"
                value="{{ old('needle_size', $yarn->needle_size ?? '') }}"
                class="mt-1 block w-full"
                placeholder="e.g. 3.5–4.0"/>
            @error('needle_size')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>


    <div>
        <label class="block text-sm font-medium text-stone-700">Color</label>
        <select name="color_id" class="mt-1 block w-full">
            <option value="">—</option>
            @foreach($colors as $c)
                <option value="{{ $c->id }}" @selected(old('color_id', $yarn->color_id ?? null) == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('color_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Material</label>
        <select name="material_id" class="mt-1 block w-full">
            <option value="">—</option>
            @foreach($materials as $m)
                <option value="{{ $m->id }}" @selected(old('material_id', $yarn->material_id ?? null) == $m->id)>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
        @error('material_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Brand</label>
        <select name="brand_id" class="mt-1 block w-full">
            <option value="">—</option>
            @foreach($brands as $b)
                <option value="{{ $b->id }}" @selected(old('brand_id', $yarn->brand_id ?? null) == $b->id)>
                    {{ $b->name }}
                </option>
            @endforeach
        </select>
        @error('brand_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Location</label>
        <select name="location_id" class="mt-1 block w-full">
            <option value="">—</option>
            @foreach($locations as $l)
                <option value="{{ $l->id }}" @selected(old('location_id', $yarn->location_id ?? null) == $l->id)>
                    {{ $l->name }}
                </option>
            @endforeach
        </select>
        @error('location_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Quantity</label>
        <input name="quantity" type="number" step="0.01" min="0.01"
               inputmode="decimal"
               value="{{ old('quantity', $yarn->quantity ?? 1) }}"
               class="mt-1 block w-full" />
        @error('quantity')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Length (m)</label>
        <input name="length_m" type="number" step="0.01" min="0"
               inputmode="decimal"
               value="{{ old('length_m', $yarn->length_m ?? '') }}"
               class="mt-1 block w-full"
               placeholder="optional" />
        @error('length_m')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Weight (g)</label>
        <input name="weight_g" type="number" step="0.01" min="0"
               inputmode="decimal"
               value="{{ old('weight_g', $yarn->weight_g ?? '') }}"
               class="mt-1 block w-full"
               placeholder="optional" />
        @error('weight_g')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <div class="mt-2 text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Details</div>
        <label class="mt-4 block text-sm font-medium text-stone-700">Notes</label>
        <textarea name="notes" rows="4"
                  class="mt-1 block w-full"
                  placeholder="optional">{{ old('notes', $yarn->notes ?? '') }}</textarea>
        @error('notes')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <label class="block text-sm font-medium text-stone-700">Photo (optional)</label>
        <p class="mt-1 text-sm text-stone-500">Neues Foto direkt aufnehmen oder aus der Galerie wählen. Vor dem Speichern siehst du sofort eine Vorschau.</p>

        <div class="mt-4 rounded-[28px] border border-stone-200 bg-stone-50/90 p-4"
             data-image-upload
             data-image-current-url="{{ $currentPhotoUrl ?? '' }}">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
                <div class="flex shrink-0 items-center justify-center">
                    @if($currentPhotoUrl)
                        <img
                            src="{{ $currentPhotoUrl }}"
                            alt="Current photo"
                            class="h-40 w-40 rounded-3xl border border-stone-200 object-cover"
                            data-image-preview
                        />
                        <div class="hidden h-40 w-40 items-center justify-center rounded-3xl border border-dashed border-stone-300 bg-white text-center text-sm text-stone-400"
                             data-image-empty>
                            Noch kein Foto
                        </div>
                    @else
                        <img
                            src=""
                            alt="Photo preview"
                            class="hidden h-40 w-40 rounded-3xl border border-stone-200 object-cover"
                            data-image-preview
                        />
                        <div class="flex h-40 w-40 items-center justify-center rounded-3xl border border-dashed border-stone-300 bg-white text-center text-sm text-stone-400"
                             data-image-empty>
                            Noch kein Foto
                        </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1 space-y-4">
                    <div class="grid gap-2 sm:grid-cols-3">
                        <button type="button" class="app-button w-full" data-image-trigger="camera">
                            Kamera
                        </button>
                        <button type="button" class="app-button-secondary w-full" data-image-trigger="gallery">
                            Galerie
                        </button>
                        <button type="button" class="app-button-secondary w-full" data-image-clear>
                            Auswahl entfernen
                        </button>
                    </div>

                    <div class="rounded-2xl bg-white px-4 py-3 text-sm text-stone-600" data-image-feedback>
                        @if($currentPhotoUrl)
                            Aktuell ist bereits ein Foto gespeichert. Neue Auswahl ersetzt das Bild erst beim Speichern.
                        @else
                            Noch keine Auswahl. Auf dem Smartphone kannst du direkt die Kamera oder Galerie öffnen.
                        @endif
                    </div>

                    <input type="file"
                           name="photo"
                           accept="image/*"
                           capture="environment"
                           class="hidden"
                           data-image-input="camera" />

                    <input type="file"
                           name="photo"
                           accept="image/*"
                           class="hidden"
                           data-image-input="gallery" />
                </div>
            </div>
        </div>

        @error('photo')
            <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
        @enderror

        @if($currentPhotoUrl)
            <div class="mt-3 text-sm text-stone-500">
                Das aktuell gespeicherte Foto bleibt erhalten, bis du ein neues Bild speicherst.
            </div>
        @endif
    </div>
</div>
