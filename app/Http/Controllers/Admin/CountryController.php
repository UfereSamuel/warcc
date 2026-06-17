<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of countries
     */
    public function index()
    {
        $countries = Country::orderBy('sort_order')->get();
        return view('admin.countries.index', compact('countries'));
    }

    /**
     * Store a newly created country
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'official_name' => 'required|string|max:255',
            'flag_code' => 'required|string|size:2',
        ]);

        // Get the next sort order
        $maxSortOrder = Country::max('sort_order');

        Country::create([
            'name' => $validated['name'],
            'official_name' => $validated['official_name'],
            'flag_code' => strtolower($validated['flag_code']),
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return back()->with('success', 'Country added successfully.');
    }

    /**
     * Update the specified country
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'official_name' => 'required|string|max:255',
            'flag_code' => 'required|string|size:2',
            'sort_order' => 'required|integer|min:0',
        ]);

        $country->update([
            'name' => $validated['name'],
            'official_name' => $validated['official_name'],
            'flag_code' => strtolower($validated['flag_code']),
            'sort_order' => $validated['sort_order'],
        ]);

        return back()->with('success', 'Country updated successfully.');
    }

    /**
     * Toggle country active status
     */
    public function toggle($id)
    {
        $country = Country::findOrFail($id);
        $country->is_active = !$country->is_active;
        $country->save();

        return back()->with('success', 'Country status updated.');
    }

    /**
     * Remove the specified country
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return back()->with('success', 'Country deleted successfully.');
    }
}
