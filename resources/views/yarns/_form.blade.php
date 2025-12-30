@php
    /** @var \App\Models\Yarn|null $yarn */
    $isEdit = isset($yarn);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label class="block text-sm font-medium text-gray-700">Project</label>
        <select name="project_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(old('project_id', $yarn->project_id ?? null) == $p->id)>
                    {{ $p->name }}
                </option>
            @endforeach
        </select>
        @error('project_id')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Yarn name</label>
        <input name="name"
            value="{{ old('name', $yarn->name ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="e.g. Weihnachtssocken (Merino Gelb)"/>
        @error('name')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Color code</label>
            <input name="color_code"
                value="{{ old('color_code', $yarn->color_code ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="e.g. 401 / A12"/>
            @error('color_code')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Batch number</label>
            <input name="batch_number"
                value="{{ old('batch_number', $yarn->batch_number ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Dye Lot / Charge"/>
            @error('batch_number')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Needle size</label>
            <input name="needle_size"
                value="{{ old('needle_size', $yarn->needle_size ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="e.g. 3.5–4.0"/>
            @error('needle_size')
                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>


    <div>
        <label class="block text-sm font-medium text-gray-700">Color</label>
        <select name="color_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
        <label class="block text-sm font-medium text-gray-700">Material</label>
        <select name="material_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
        <label class="block text-sm font-medium text-gray-700">Brand</label>
        <select name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <select name="location_id" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
        <label class="block text-sm font-medium text-gray-700">Quantity</label>
        <input name="quantity" type="number" step="0.01" min="0.01"
               value="{{ old('quantity', $yarn->quantity ?? 1) }}"
               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
        @error('quantity')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Length (m)</label>
        <input name="length_m" type="number" step="0.01" min="0"
               value="{{ old('length_m', $yarn->length_m ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="optional" />
        @error('length_m')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Weight (g)</label>
        <input name="weight_g" type="number" step="0.01" min="0"
               value="{{ old('weight_g', $yarn->weight_g ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="optional" />
        @error('weight_g')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="optional">{{ old('notes', $yarn->notes ?? '') }}</textarea>
        @error('notes')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Photo (optional)</label>
        <input type="file" name="photo" accept="image/*;capture=camera"
            class="mt-1 block w-full text-sm text-gray-700" />
        @error('photo')
            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror

        @if(!empty($yarn?->photo_path))
            <img
                src="{{ Storage::url($yarn->photo_path) }}"
                alt="Current photo"
                class="mt-2 h-32 w-32 rounded border object-cover"
            />
        @endif
    </div>
</div>
