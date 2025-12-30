<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Location;
use App\Models\Material;
use App\Models\Project;
use App\Models\Yarn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;

class YarnController extends Controller
{
    public function index()
    {
        $yarns = Yarn::query()
            ->where('user_id', auth()->id())
            ->with(['project', 'color', 'material', 'brand', 'location'])
            ->latest('id')
            ->paginate(20);

        return view('yarns.index', compact('yarns'));
    }

    public function create()
    {
        return view('yarns.create', $this->metaOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validateYarn($request);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->storeYarnPhoto($request->file('photo'));
        }

        $yarn = Yarn::query()->create([
            'user_id'       => auth()->id(),
            'project_id'    => $data['project_id'] ?? null,
            'color_id'      => $data['color_id'] ?? null,
            'material_id'   => $data['material_id'] ?? null,
            'brand_id'      => $data['brand_id'] ?? null,
            'location_id'   => $data['location_id'] ?? null,

            'name'          => $data['name'] ?? null,
            'color_code'    => $data['color_code'] ?? null,
            'batch_number'  => $data['batch_number'] ?? null,
            'needle_size'   => $data['needle_size'] ?? null,

            'quantity'      => $data['quantity'],
            'length_m'      => $data['length_m'] ?? null,
            'weight_g'      => $data['weight_g'] ?? null,
            'notes'         => $data['notes'] ?? null,

            'photo_path'    => $photoPath,
        ]);

        return redirect()->route('yarns.edit', $yarn)->with('status', 'Yarn saved.');
    }

    public function edit(Yarn $yarn)
    {
        $this->authorize('update', $yarn);

        $yarn->load(['project', 'color', 'material', 'brand', 'location']);

        return view('yarns.edit', array_merge(
            ['yarn' => $yarn],
            $this->metaOptions()
        ));
    }

    public function update(Request $request, Yarn $yarn)
    {
        $this->authorize('update', $yarn);

        $data = $this->validateYarn($request);

        $photoPath = $yarn->photo_path;
        if ($request->hasFile('photo')) {
            // delete old
            if ($yarn->photo_path) {
                Storage::disk('public')->delete($yarn->photo_path);
            }
            $photoPath = $this->storeYarnPhoto($request->file('photo'));
        }

        $yarn->update([
            'project_id'    => $data['project_id'] ?? null,
            'color_id'      => $data['color_id'] ?? null,
            'material_id'   => $data['material_id'] ?? null,
            'brand_id'      => $data['brand_id'] ?? null,
            'location_id'   => $data['location_id'] ?? null,

            'name'          => $data['name'] ?? null,
            'color_code'    => $data['color_code'] ?? null,
            'batch_number'  => $data['batch_number'] ?? null,
            'needle_size'   => $data['needle_size'] ?? null,

            'quantity'      => $data['quantity'],
            'length_m'      => $data['length_m'] ?? null,
            'weight_g'      => $data['weight_g'] ?? null,
            'notes'         => $data['notes'] ?? null,

            'photo_path'    => $photoPath,
        ]);

        return redirect()->route('yarns.edit', $yarn)->with('status', 'Yarn updated.');
    }

    public function destroy(Yarn $yarn)
    {
        $this->authorize('delete', $yarn);

        if ($yarn->photo_path) {
            Storage::disk('public')->delete($yarn->photo_path);
        }

        $yarn->delete();

        return redirect()->route('yarns.index')->with('status', 'Yarn deleted.');
    }

    private function metaOptions(): array
    {
        $userId = auth()->id();

        return [
            'projects'  => Project::query()->where('user_id', $userId)->orderBy('name')->get(),
            'colors'    => Color::query()->where('user_id', $userId)->orderBy('name')->get(),
            'materials' => Material::query()->where('user_id', $userId)->orderBy('name')->get(),
            'brands'    => Brand::query()->where('user_id', $userId)->orderBy('name')->get(),
            'locations' => Location::query()->where('user_id', $userId)->orderBy('name')->get(),
        ];
    }

    private function validateYarn(Request $request): array
    {
        $userId = auth()->id();

        return $request->validate([
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')->where('user_id', $userId)],
            'color_id' => ['nullable', 'integer', Rule::exists('colors', 'id')->where('user_id', $userId)],
            'material_id' => ['nullable', 'integer', Rule::exists('materials', 'id')->where('user_id', $userId)],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')->where('user_id', $userId)],
            'location_id' => ['nullable', 'integer', Rule::exists('locations', 'id')->where('user_id', $userId)],

            'name'         => ['required', 'string', 'max:120'],
            'color_code'   => ['nullable', 'string', 'max:30'],
            'batch_number' => ['nullable', 'string', 'max:60'],
            'needle_size'  => ['nullable', 'string', 'max:10'],

            'quantity' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'length_m' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'weight_g' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],

            // Photo: bewusst eingeschränkt (HEIC von iPhone ist je nach Server-Setup schwierig)
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            'notes' => ['nullable', 'string'],
        ]);
    }

    private function storeYarnPhoto(\Illuminate\Http\UploadedFile $file): string
    {
        // Ziel: max Breite 1200px, proportional, als WebP speichern
        $img = Image::read($file)->scaleDown(width: 800); // proportionale Verkleinerung :contentReference[oaicite:3]{index=3}

        $encoded = $img->toWebp(quality: 80);

        $dir = 'uploads/yarns';
        $filename = 'yarn_' . uniqid('', true) . '.webp';
        $path = $dir . '/' . $filename;

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}
