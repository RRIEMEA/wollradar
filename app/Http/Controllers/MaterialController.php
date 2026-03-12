<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('materials.index', compact('materials'));
    }

    public function store(Request $request)
    {
        if ($request->boolean('quick_add')) {
            return $this->storeQuickAdd($request);
        }

        $userId = auth()->id();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('materials', 'name')->where('user_id', $userId),
            ],
        ]);

        $name = trim($data['name']);
        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Name ist erforderlich.'])
                ->withInput();
        }

        Material::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('materials.index')->with('status', 'Material gespeichert.');
    }

    public function destroy(Material $material)
    {
        $this->authorize('delete', $material);

        $material->delete();

        return redirect()->route('materials.index')->with('status', 'Material gelöscht.');
    }

    private function storeQuickAdd(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'quick_material_name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('materials', 'name')->where('user_id', $userId),
            ],
        ]);

        if ($validator->fails()) {
            return $this->quickCreateValidationRedirect(
                $request,
                'materials.index',
                $validator,
                'quickAddMaterial',
                'quick-add-material'
            );
        }

        $material = Material::query()->create([
            'user_id' => $userId,
            'name' => trim((string) $request->input('quick_material_name')),
        ]);

        return $this->quickCreateSuccessRedirect(
            $request,
            'materials.index',
            'Material angelegt.',
            'material_id',
            $material->id
        );
    }
}
