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
    public function index(Request $request)
    {
        $userId = auth()->id();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')->where('user_id', $userId)],
            'color_id' => ['nullable', 'integer', Rule::exists('colors', 'id')->where('user_id', $userId)],
            'material_id' => ['nullable', 'integer', Rule::exists('materials', 'id')->where('user_id', $userId)],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')->where('user_id', $userId)],
            'location_id' => ['nullable', 'integer', Rule::exists('locations', 'id')->where('user_id', $userId)],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name_asc', 'name_desc', 'quantity_desc', 'quantity_asc', 'updated_desc'])],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $sort = $filters['sort'] ?? 'newest';
        $selectedRelationFilters = collect($filters)->except(['q', 'sort'])->filter();
        $hasFilters = $search !== '' || $selectedRelationFilters->isNotEmpty();

        $yarnsQuery = Yarn::query()
            ->where('user_id', auth()->id())
            ->with(['project', 'color', 'material', 'brand', 'location'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $like = '%' . $search . '%';

                    $nested->where('name', 'like', $like)
                        ->orWhere('color_code', 'like', $like)
                        ->orWhere('batch_number', 'like', $like)
                        ->orWhere('needle_size', 'like', $like)
                        ->orWhere('notes', 'like', $like)
                        ->orWhereHas('project', fn ($project) => $project->where('name', 'like', $like))
                        ->orWhereHas('color', fn ($color) => $color->where('name', 'like', $like))
                        ->orWhereHas('material', fn ($material) => $material->where('name', 'like', $like))
                        ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', $like))
                        ->orWhereHas('location', fn ($location) => $location->where('name', 'like', $like));
                });
            })
            ->when(!empty($filters['project_id']), fn ($query) => $query->where('project_id', $filters['project_id']))
            ->when(!empty($filters['color_id']), fn ($query) => $query->where('color_id', $filters['color_id']))
            ->when(!empty($filters['material_id']), fn ($query) => $query->where('material_id', $filters['material_id']))
            ->when(!empty($filters['brand_id']), fn ($query) => $query->where('brand_id', $filters['brand_id']))
            ->when(!empty($filters['location_id']), fn ($query) => $query->where('location_id', $filters['location_id']));

        $yarnsQuery = match ($sort) {
            'oldest' => $yarnsQuery->oldest('id'),
            'name_asc' => $yarnsQuery->orderByRaw('CASE WHEN name IS NULL OR name = "" THEN 1 ELSE 0 END')->orderBy('name'),
            'name_desc' => $yarnsQuery->orderByRaw('CASE WHEN name IS NULL OR name = "" THEN 1 ELSE 0 END')->orderByDesc('name'),
            'quantity_desc' => $yarnsQuery->orderByDesc('quantity')->latest('id'),
            'quantity_asc' => $yarnsQuery->orderBy('quantity')->latest('id'),
            'updated_desc' => $yarnsQuery->latest('updated_at')->latest('id'),
            default => $yarnsQuery->latest('id'),
        };

        $yarns = $yarnsQuery
            ->paginate(20)
            ->withQueryString();

        return view('yarns.index', array_merge($this->metaOptions(), [
            'yarns' => $yarns,
            'filters' => [
                'q' => $search,
                'project_id' => $filters['project_id'] ?? null,
                'color_id' => $filters['color_id'] ?? null,
                'material_id' => $filters['material_id'] ?? null,
                'brand_id' => $filters['brand_id'] ?? null,
                'location_id' => $filters['location_id'] ?? null,
                'sort' => $sort,
            ],
            'hasFilters' => $hasFilters,
            'hasActiveControls' => $hasFilters || $sort !== 'newest',
            'activeFilterCount' => ($search !== '' ? 1 : 0) + $selectedRelationFilters->count(),
            'totalYarnCount' => Yarn::query()->where('user_id', $userId)->count(),
        ]));
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
            'is_finished'   => (bool) ($data['is_finished'] ?? false),

            'photo_path'    => $photoPath,
        ]);

        return redirect()->route('yarns.edit', $yarn)->with('status', 'Garn gespeichert.');
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

    public function adjustQuantity(Request $request, Yarn $yarn)
    {
        $this->authorize('update', $yarn);

        $data = $request->validate([
            'direction' => ['required', Rule::in(['increment', 'decrement'])],
        ]);

        $delta = $data['direction'] === 'increment' ? 0.5 : -0.5;
        $updatedQuantity = max(0.5, round(((float) $yarn->quantity) + $delta, 2));

        $yarn->update([
            'quantity' => $updatedQuantity,
        ]);

        return back();
    }

    public function finishProject(Yarn $yarn)
    {
        $this->authorize('update', $yarn);

        $yarn->loadMissing('project');

        if (! $yarn->project) {
            return back()->with('status', 'Kein Projekt zugeordnet.');
        }

        $yarn->update([
            'is_finished' => true,
        ]);

        $yarn->project->update([
            'is_finished' => true,
        ]);

        return back()->with('status', 'Projekt fertig markiert.');
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
            'is_finished'   => (bool) ($data['is_finished'] ?? false),

            'photo_path'    => $photoPath,
        ]);

        return redirect()->route('yarns.edit', $yarn)->with('status', 'Garn aktualisiert.');
    }

    public function destroy(Yarn $yarn)
    {
        $this->authorize('delete', $yarn);

        if ($yarn->photo_path) {
            Storage::disk('public')->delete($yarn->photo_path);
        }

        $yarn->delete();

        return redirect()->route('yarns.index')->with('status', 'Garn gelöscht.');
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

            'quantity' => ['required', 'numeric', 'min:0.5', 'max:99999999.99'],
            'length_m' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'weight_g' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],

            // Photo: bewusst eingeschränkt (HEIC von iPhone ist je nach Server-Setup schwierig)
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            'notes' => ['nullable', 'string'],
            'is_finished' => ['nullable', 'boolean'],
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
