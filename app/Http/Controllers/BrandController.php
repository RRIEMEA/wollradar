<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        if ($request->boolean('quick_add')) {
            return $this->storeQuickAdd($request);
        }

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
                ->withErrors(['name' => 'Name ist erforderlich.'])
                ->withInput();
        }

        // Optional: Policy für "create" (wenn du das in BrandPolicy definiert hast)
        // $this->authorize('create', Brand::class);

        Brand::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('brands.index')->with('status', 'Marke gespeichert.');
    }

    public function destroy(Brand $brand)
    {
        // Policy statt abort_unless
        $this->authorize('delete', $brand);

        $brand->delete();

        return redirect()->route('brands.index')->with('status', 'Marke gelöscht.');
    }

    private function storeQuickAdd(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'quick_brand_name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('brands', 'name')->where('user_id', $userId),
            ],
        ]);

        if ($validator->fails()) {
            return $this->quickCreateValidationRedirect(
                $request,
                'brands.index',
                $validator,
                'quickAddBrand',
                'quick-add-brand'
            );
        }

        $brand = Brand::query()->create([
            'user_id' => $userId,
            'name' => trim((string) $request->input('quick_brand_name')),
        ]);

        return $this->quickCreateSuccessRedirect(
            $request,
            'brands.index',
            'Marke angelegt.',
            'brand_id',
            $brand->id
        );
    }
}
