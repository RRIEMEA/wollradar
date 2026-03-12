<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('colors.index', compact('colors'));
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
                Rule::unique('colors', 'name')->where('user_id', $userId),
            ],
        ]);

        $name = trim($data['name']);
        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Name ist erforderlich.'])
                ->withInput();
        }

        Color::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('colors.index')->with('status', 'Farbe gespeichert.');
    }

    public function destroy(Color $color)
    {
        $this->authorize('delete', $color);

        $color->delete();

        return redirect()->route('colors.index')->with('status', 'Farbe gelöscht.');
    }

    private function storeQuickAdd(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'quick_color_name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('colors', 'name')->where('user_id', $userId),
            ],
        ]);

        if ($validator->fails()) {
            return $this->quickCreateValidationRedirect(
                $request,
                'colors.index',
                $validator,
                'quickAddColor',
                'quick-add-color'
            );
        }

        $color = Color::query()->create([
            'user_id' => $userId,
            'name' => trim((string) $request->input('quick_color_name')),
        ]);

        return $this->quickCreateSuccessRedirect(
            $request,
            'colors.index',
            'Farbe angelegt.',
            'color_id',
            $color->id
        );
    }
}
