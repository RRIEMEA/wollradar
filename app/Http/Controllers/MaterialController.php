<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                ->withErrors(['name' => 'Name is required.'])
                ->withInput();
        }

        Material::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('materials.index')->with('status', 'Material saved.');
    }

    public function destroy(Material $material)
    {
        $this->authorize('delete', $material);

        $material->delete();

        return redirect()->route('materials.index')->with('status', 'Material deleted.');
    }
}
