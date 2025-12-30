<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('locations', 'name')->where('user_id', $userId),
            ],
        ]);

        $name = trim($data['name']);
        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Name is required.'])
                ->withInput();
        }

        Location::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('locations.index')->with('status', 'Location saved.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        $location->delete();

        return redirect()->route('locations.index')->with('status', 'Location deleted.');
    }
}
