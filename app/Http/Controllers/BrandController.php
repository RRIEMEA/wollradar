<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        // Liste ist immer "owned by user", Policy ist hier optional.
        $brands = Brand::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                // Unique pro User (Case-Sensitivity hängt von DB/Collation ab)
                Rule::unique('brands', 'name')->where('user_id', $userId),
            ],
        ]);

        $name = trim($data['name']);

        // Optional: Leere Strings nach trim verhindern
        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Name is required.'])
                ->withInput();
        }

        // Optional: Policy für "create" (wenn du das in BrandPolicy definiert hast)
        // $this->authorize('create', Brand::class);

        Brand::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('brands.index')->with('status', 'Brand saved.');
    }

    public function destroy(Brand $brand)
    {
        // Policy statt abort_unless
        $this->authorize('delete', $brand);

        $brand->delete();

        return redirect()->route('brands.index')->with('status', 'Brand deleted.');
    }
}
