<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        if ($request->boolean('quick_add')) {
            return $this->storeQuickAdd($request);
        }

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
                ->withErrors(['name' => 'Name ist erforderlich.'])
                ->withInput();
        }

        Location::query()->create([
            'user_id' => $userId,
            'name'    => $name,
        ]);

        return redirect()->route('locations.index')->with('status', 'Ort gespeichert.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        $location->delete();

        return redirect()->route('locations.index')->with('status', 'Ort gelöscht.');
    }

    private function storeQuickAdd(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'quick_location_name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('locations', 'name')->where('user_id', $userId),
            ],
        ]);

        if ($validator->fails()) {
            return $this->quickCreateValidationRedirect(
                $request,
                'locations.index',
                $validator,
                'quickAddLocation',
                'quick-add-location'
            );
        }

        $location = Location::query()->create([
            'user_id' => $userId,
            'name' => trim((string) $request->input('quick_location_name')),
        ]);

        return $this->quickCreateSuccessRedirect(
            $request,
            'locations.index',
            'Ort angelegt.',
            'location_id',
            $location->id
        );
    }
}
