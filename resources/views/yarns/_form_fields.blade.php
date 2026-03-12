@php
    /** @var \App\Models\Yarn|null $yarn */
    $isEdit = isset($yarn);
    $currentPhotoUrl = !empty($yarn?->photo_path) ? Storage::url($yarn->photo_path) : null;
    $quickAddSelected = session('quick_add_selected', []);
    $prefill = $isEdit ? [] : request()->only(['project_id', 'color_id', 'material_id', 'brand_id', 'location_id']);
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

    <div class="md:col-span-2 xl:col-span-3">
        <div class="text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Grunddaten</div>
        <p class="mt-2 text-sm text-stone-600">Die wichtigsten Angaben zuerst. Auf dem Handy sind alle Felder mit großen Touch-Zielen formatiert.</p>
    </div>

    <x-yarn-related-select
        field="project_id"
        label="Projekt"
        :options="$projects"
        :selected="$quickAddSelected['project_id'] ?? ($prefill['project_id'] ?? ($yarn->project_id ?? null))"
        modalName="quick-add-project"
        helper="Wenn das Projekt fehlt, kannst du es direkt aus dem Formular heraus anlegen."
    />

    <div class="md:col-span-1 xl:col-span-2">
        <label class="block text-sm font-medium text-stone-700">Garnname</label>
        <input name="name"
            value="{{ old('name', $yarn->name ?? '') }}"
            class="mt-1 block w-full"
            placeholder="z. B. Weihnachtssocken (Merino Gelb)"/>
        @error('name')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-stone-700">Farbcode</label>
            <input name="color_code"
                value="{{ old('color_code', $yarn->color_code ?? '') }}"
                class="mt-1 block w-full"
                placeholder="z. B. 401 / A12"/>
            @error('color_code')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Chargennummer</label>
            <input name="batch_number"
                value="{{ old('batch_number', $yarn->batch_number ?? '') }}"
                class="mt-1 block w-full"
                placeholder="Partie / Charge"/>
            @error('batch_number')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-stone-700">Nadelstärke</label>
            <input name="needle_size"
                value="{{ old('needle_size', $yarn->needle_size ?? '') }}"
                class="mt-1 block w-full"
                placeholder="z. B. 3,5–4,0"/>
            @error('needle_size')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>


    <x-yarn-related-select
        field="color_id"
        label="Farbe"
        :options="$colors"
        :selected="$quickAddSelected['color_id'] ?? ($prefill['color_id'] ?? ($yarn->color_id ?? null))"
        modalName="quick-add-color"
    />

    <x-yarn-related-select
        field="material_id"
        label="Material"
        :options="$materials"
        :selected="$quickAddSelected['material_id'] ?? ($prefill['material_id'] ?? ($yarn->material_id ?? null))"
        modalName="quick-add-material"
    />

    <x-yarn-related-select
        field="brand_id"
        label="Marke"
        :options="$brands"
        :selected="$quickAddSelected['brand_id'] ?? ($prefill['brand_id'] ?? ($yarn->brand_id ?? null))"
        modalName="quick-add-brand"
    />

    <x-yarn-related-select
        field="location_id"
        label="Ort"
        :options="$locations"
        :selected="$quickAddSelected['location_id'] ?? ($prefill['location_id'] ?? ($yarn->location_id ?? null))"
        modalName="quick-add-location"
    />

    <div>
        <label class="block text-sm font-medium text-stone-700">Menge</label>
        <input name="quantity" type="number" step="0.5" min="0.5"
               inputmode="decimal"
               value="{{ old('quantity', $yarn->quantity ?? 1) }}"
               class="mt-1 block w-full" />
        @error('quantity')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Länge (m)</label>
        <input name="length_m" type="number" step="0.01" min="0"
               inputmode="decimal"
               value="{{ old('length_m', $yarn->length_m ?? '') }}"
               class="mt-1 block w-full"
               placeholder="optional" />
        @error('length_m')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-700">Gewicht (g)</label>
        <input name="weight_g" type="number" step="0.01" min="0"
               inputmode="decimal"
               value="{{ old('weight_g', $yarn->weight_g ?? '') }}"
               class="mt-1 block w-full"
               placeholder="optional" />
        @error('weight_g')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <div class="mt-2 text-sm font-semibold uppercase tracking-[0.24em] text-stone-500">Details</div>
        <label class="mt-4 block text-sm font-medium text-stone-700">Notizen</label>
        <textarea name="notes" rows="4"
                  class="mt-1 block w-full"
                  placeholder="optional">{{ old('notes', $yarn->notes ?? '') }}</textarea>
        @error('notes')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <label class="flex items-center gap-3 rounded-3xl border border-stone-200 bg-stone-50/80 px-4 py-4">
            <input
                type="checkbox"
                name="is_finished"
                value="1"
                class="h-5 w-5 rounded border-stone-300 text-amber-700 focus:ring-amber-500"
                @checked(old('is_finished', $yarn->is_finished ?? false))
            />
            <span>
                <span class="block text-sm font-medium text-stone-900">Projekt fertig</span>
                <span class="block text-sm text-stone-500">Markiert das dazugehörige Projekt als abgeschlossen.</span>
            </span>
        </label>
        @error('is_finished')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2 xl:col-span-3">
        <label class="block text-sm font-medium text-stone-700">Foto (optional)</label>
        <p class="mt-1 text-sm text-stone-500">Neues Foto direkt aufnehmen oder aus der Galerie wählen. Vor dem Speichern siehst du sofort eine Vorschau.</p>

        <div class="mt-4 rounded-[28px] border border-stone-200 bg-stone-50/90 p-4"
             data-image-upload
             data-image-current-url="{{ $currentPhotoUrl ?? '' }}">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
                <div class="flex shrink-0 items-center justify-center">
                    @if($currentPhotoUrl)
                        <img
                            src="{{ $currentPhotoUrl }}"
                            alt="Aktuelles Foto"
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
                            alt="Foto-Vorschau"
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
