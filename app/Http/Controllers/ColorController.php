<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                ->withErrors(['name' => 'Name is required.'])
                ->withInput();
        }

        Color::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('colors.index')->with('status', 'Color saved.');
    }

    public function destroy(Color $color)
    {
        $this->authorize('delete', $color);

        $color->delete();

        return redirect()->route('colors.index')->with('status', 'Color deleted.');
    }
}
