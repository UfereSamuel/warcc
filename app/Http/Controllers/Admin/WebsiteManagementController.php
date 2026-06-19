<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Setting;
use App\Services\HomepageContentService;
use Illuminate\Http\Request;

class WebsiteManagementController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('sort_order')->get();
        $content = HomepageContentService::forAdmin();

        return view('admin.website-management.index', compact('countries', 'content'));
    }

    public function updateDefaultHero(Request $request)
    {
        $validated = $request->validate([
            'default_hero_title' => 'required|string|max:255',
            'default_hero_description' => 'required|string|max:2000',
        ]);

        Setting::set('homepage_default_hero_title', $validated['default_hero_title']);
        Setting::set('homepage_default_hero_description', $validated['default_hero_description']);

        return back()->with('success', 'Default hero section updated successfully.');
    }

    public function updateVisionMission(Request $request)
    {
        $validated = $request->validate([
            'organization_mission_title' => 'required|string|max:255',
            'organization_mission_text' => 'required|string|max:3000',
            'organization_vision_title' => 'required|string|max:255',
            'organization_vision_text' => 'required|string|max:3000',
        ]);

        Setting::set('organization_mission_title', $validated['organization_mission_title']);
        Setting::set('organization_mission_text', $validated['organization_mission_text']);
        Setting::set('organization_vision_title', $validated['organization_vision_title']);
        Setting::set('organization_vision_text', $validated['organization_vision_text']);

        return back()->with('success', 'Vision and mission statements updated successfully.');
    }

    public function updateServingSection(Request $request)
    {
        $validated = $request->validate([
            'serving_title' => 'required|string|max:255',
            'serving_description' => 'required|string|max:2000',
        ]);

        Setting::set('homepage_serving_title', $validated['serving_title']);
        Setting::set('homepage_serving_description', $validated['serving_description']);

        return back()->with('success', 'Serving West Africa section updated successfully.');
    }

    public function updateMissionSection(Request $request)
    {
        $validated = $request->validate([
            'mission_title' => 'required|string|max:255',
            'mission_description' => 'required|string|max:2000',
            'mission_card_1_title' => 'required|string|max:255',
            'mission_card_1_text' => 'required|string|max:1000',
            'mission_card_2_title' => 'required|string|max:255',
            'mission_card_2_text' => 'required|string|max:1000',
            'mission_card_3_title' => 'required|string|max:255',
            'mission_card_3_text' => 'required|string|max:1000',
        ]);

        Setting::set('homepage_mission_title', $validated['mission_title']);
        Setting::set('homepage_mission_description', $validated['mission_description']);
        Setting::set('homepage_mission_card_1_title', $validated['mission_card_1_title']);
        Setting::set('homepage_mission_card_1_text', $validated['mission_card_1_text']);
        Setting::set('homepage_mission_card_2_title', $validated['mission_card_2_title']);
        Setting::set('homepage_mission_card_2_text', $validated['mission_card_2_text']);
        Setting::set('homepage_mission_card_3_title', $validated['mission_card_3_title']);
        Setting::set('homepage_mission_card_3_text', $validated['mission_card_3_text']);

        return back()->with('success', 'Mission focus section updated successfully.');
    }

    public function updateCoreValuesSection(Request $request)
    {
        $validated = $request->validate([
            'core_values_title' => 'required|string|max:255',
            'core_values_description' => 'required|string|max:2000',
            'core_values_card_1_title' => 'required|string|max:255',
            'core_values_card_1_text' => 'required|string|max:1000',
            'core_values_card_2_title' => 'required|string|max:255',
            'core_values_card_2_text' => 'required|string|max:1000',
            'core_values_card_3_title' => 'required|string|max:255',
            'core_values_card_3_text' => 'required|string|max:1000',
        ]);

        Setting::set('homepage_core_values_title', $validated['core_values_title']);
        Setting::set('homepage_core_values_description', $validated['core_values_description']);
        Setting::set('homepage_core_values_card_1_title', $validated['core_values_card_1_title']);
        Setting::set('homepage_core_values_card_1_text', $validated['core_values_card_1_text']);
        Setting::set('homepage_core_values_card_2_title', $validated['core_values_card_2_title']);
        Setting::set('homepage_core_values_card_2_text', $validated['core_values_card_2_text']);
        Setting::set('homepage_core_values_card_3_title', $validated['core_values_card_3_title']);
        Setting::set('homepage_core_values_card_3_text', $validated['core_values_card_3_text']);

        return back()->with('success', 'Core values section updated successfully.');
    }

    public function updateFeaturedEventsSection(Request $request)
    {
        $validated = $request->validate([
            'featured_events_title' => 'required|string|max:255',
            'featured_events_description' => 'required|string|max:2000',
        ]);

        Setting::set('homepage_featured_events_title', $validated['featured_events_title']);
        Setting::set('homepage_featured_events_description', $validated['featured_events_description']);

        return back()->with('success', 'Featured events section updated successfully.');
    }
}
