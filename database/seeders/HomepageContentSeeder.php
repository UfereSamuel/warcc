<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\HomepageContentService;
use Illuminate\Database\Seeder;

class HomepageContentSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = HomepageContentService::defaults();

        $settings = [
            ['key' => 'homepage_default_hero_title', 'value' => $defaults['default_hero_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Default Hero Title', 'sort_order' => 1],
            ['key' => 'homepage_default_hero_description', 'value' => $defaults['default_hero_description'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Default Hero Description', 'sort_order' => 2],
            ['key' => 'organization_mission_title', 'value' => $defaults['organization_mission_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Mission Statement Title', 'sort_order' => 3],
            ['key' => 'organization_mission_text', 'value' => $defaults['organization_mission_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Mission Statement Text', 'sort_order' => 4],
            ['key' => 'organization_vision_title', 'value' => $defaults['organization_vision_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Vision Statement Title', 'sort_order' => 5],
            ['key' => 'organization_vision_text', 'value' => $defaults['organization_vision_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Vision Statement Text', 'sort_order' => 6],
            ['key' => 'homepage_mission_title', 'value' => $defaults['mission_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Mission Focus Section Title', 'sort_order' => 7],
            ['key' => 'homepage_mission_description', 'value' => $defaults['mission_description'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Mission Focus Section Description', 'sort_order' => 8],
            ['key' => 'homepage_mission_card_1_title', 'value' => $defaults['mission_card_1_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Mission Focus Card 1 Title', 'sort_order' => 9],
            ['key' => 'homepage_mission_card_1_text', 'value' => $defaults['mission_card_1_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Mission Focus Card 1 Text', 'sort_order' => 10],
            ['key' => 'homepage_mission_card_2_title', 'value' => $defaults['mission_card_2_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Mission Focus Card 2 Title', 'sort_order' => 11],
            ['key' => 'homepage_mission_card_2_text', 'value' => $defaults['mission_card_2_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Mission Focus Card 2 Text', 'sort_order' => 12],
            ['key' => 'homepage_mission_card_3_title', 'value' => $defaults['mission_card_3_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Mission Focus Card 3 Title', 'sort_order' => 13],
            ['key' => 'homepage_mission_card_3_text', 'value' => $defaults['mission_card_3_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Mission Focus Card 3 Text', 'sort_order' => 14],
            ['key' => 'homepage_serving_title', 'value' => $defaults['serving_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Serving Section Title', 'sort_order' => 15],
            ['key' => 'homepage_serving_description', 'value' => $defaults['serving_description'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Serving Section Description', 'description' => 'Use {count} as a placeholder for the number of active countries', 'sort_order' => 16],
            ['key' => 'homepage_featured_events_title', 'value' => $defaults['featured_events_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Featured Events Title', 'sort_order' => 17],
            ['key' => 'homepage_featured_events_description', 'value' => $defaults['featured_events_description'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Featured Events Description', 'sort_order' => 18],
            ['key' => 'homepage_core_values_title', 'value' => $defaults['core_values_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Core Values Title', 'sort_order' => 19],
            ['key' => 'homepage_core_values_description', 'value' => $defaults['core_values_description'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Core Values Description', 'sort_order' => 20],
            ['key' => 'homepage_core_values_card_1_title', 'value' => $defaults['core_values_card_1_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Core Values Card 1 Title', 'sort_order' => 21],
            ['key' => 'homepage_core_values_card_1_text', 'value' => $defaults['core_values_card_1_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Core Values Card 1 Text', 'sort_order' => 22],
            ['key' => 'homepage_core_values_card_2_title', 'value' => $defaults['core_values_card_2_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Core Values Card 2 Title', 'sort_order' => 23],
            ['key' => 'homepage_core_values_card_2_text', 'value' => $defaults['core_values_card_2_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Core Values Card 2 Text', 'sort_order' => 24],
            ['key' => 'homepage_core_values_card_3_title', 'value' => $defaults['core_values_card_3_title'], 'type' => 'text', 'group' => 'homepage', 'label' => 'Core Values Card 3 Title', 'sort_order' => 25],
            ['key' => 'homepage_core_values_card_3_text', 'value' => $defaults['core_values_card_3_text'], 'type' => 'textarea', 'group' => 'homepage', 'label' => 'Core Values Card 3 Text', 'sort_order' => 26],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
