<?php

namespace App\Services;

use App\Models\Setting;

class HomepageContentService
{
    public static function defaults(): array
    {
        return [
            'default_hero_title' => 'Africa CDC Western RCC',
            'default_hero_description' => 'Strengthening health security and disease surveillance across West Africa through collaborative partnerships, capacity building, and innovative health solutions.',
            'organization_mission_title' => 'Our Mission',
            'organization_mission_text' => 'To strengthen health security and disease surveillance capacity across West Africa through collaborative partnerships, technical assistance, and capacity building initiatives that support member states in building resilient health systems.',
            'organization_vision_title' => 'Our Vision',
            'organization_vision_text' => 'A West Africa region with robust health systems capable of preventing, detecting, and responding to disease outbreaks and health emergencies, ensuring the health and well-being of all populations.',
            'mission_title' => 'Our Mission',
            'mission_description' => 'Coordinating regional health initiatives and supporting member states in building resilient health systems for better health outcomes across West Africa.',
            'mission_card_1_title' => 'Disease Surveillance',
            'mission_card_1_text' => 'Advanced monitoring and early warning systems for disease outbreaks and health emergencies.',
            'mission_card_2_title' => 'Capacity Building',
            'mission_card_2_text' => 'Training and development programs to strengthen health workforce capabilities across the region.',
            'mission_card_3_title' => 'Regional Coordination',
            'mission_card_3_text' => 'Facilitating collaboration and coordination among West African health systems and institutions.',
            'serving_title' => 'Serving West Africa',
            'serving_description' => 'The Western RCC serves {count} countries across West Africa, working together to strengthen regional health security and build resilient health systems.',
            'featured_events_title' => 'Featured Events',
            'featured_events_description' => "Don't miss these upcoming events and opportunities to strengthen health security across West Africa",
            'core_values_title' => 'Our Core Values',
            'core_values_description' => 'Guiding principles that drive our commitment to health security in West Africa',
            'core_values_card_1_title' => 'Collaboration',
            'core_values_card_1_text' => 'Working together across borders to achieve common health security goals.',
            'core_values_card_2_title' => 'Innovation',
            'core_values_card_2_text' => 'Embracing cutting-edge solutions and technologies for better health outcomes.',
            'core_values_card_3_title' => 'Excellence',
            'core_values_card_3_text' => 'Maintaining the highest standards in all our health security initiatives.',
        ];
    }

    public static function forAdmin(): array
    {
        $defaults = self::defaults();

        return [
            'default_hero_title' => Setting::get('homepage_default_hero_title', $defaults['default_hero_title']),
            'default_hero_description' => Setting::get('homepage_default_hero_description', $defaults['default_hero_description']),
            'organization_mission_title' => Setting::get('organization_mission_title', $defaults['organization_mission_title']),
            'organization_mission_text' => Setting::get('organization_mission_text', $defaults['organization_mission_text']),
            'organization_vision_title' => Setting::get('organization_vision_title', $defaults['organization_vision_title']),
            'organization_vision_text' => Setting::get('organization_vision_text', $defaults['organization_vision_text']),
            'mission_title' => Setting::get('homepage_mission_title', $defaults['mission_title']),
            'mission_description' => Setting::get('homepage_mission_description', $defaults['mission_description']),
            'mission_cards' => self::missionCards(),
            'serving_title' => Setting::get('homepage_serving_title', $defaults['serving_title']),
            'serving_description' => Setting::get('homepage_serving_description', $defaults['serving_description']),
            'featured_events_title' => Setting::get('homepage_featured_events_title', $defaults['featured_events_title']),
            'featured_events_description' => Setting::get('homepage_featured_events_description', $defaults['featured_events_description']),
            'core_values_title' => Setting::get('homepage_core_values_title', $defaults['core_values_title']),
            'core_values_description' => Setting::get('homepage_core_values_description', $defaults['core_values_description']),
            'core_values_cards' => self::coreValuesCards(),
        ];
    }

    public static function forPublic(?int $countryCount = null): array
    {
        $content = self::forAdmin();

        if ($countryCount !== null) {
            $content['serving_description'] = str_replace('{count}', (string) $countryCount, $content['serving_description']);
        }

        return $content;
    }

    public static function organizationStatements(): array
    {
        $defaults = self::defaults();

        return [
            'mission_title' => Setting::get('organization_mission_title', $defaults['organization_mission_title']),
            'mission_text' => Setting::get('organization_mission_text', $defaults['organization_mission_text']),
            'vision_title' => Setting::get('organization_vision_title', $defaults['organization_vision_title']),
            'vision_text' => Setting::get('organization_vision_text', $defaults['organization_vision_text']),
        ];
    }

    /**
     * @return list<array{title: string, text: string, icon: string}>
     */
    private static function missionCards(): array
    {
        $defaults = self::defaults();

        return [
            [
                'title' => Setting::get('homepage_mission_card_1_title', $defaults['mission_card_1_title']),
                'text' => Setting::get('homepage_mission_card_1_text', $defaults['mission_card_1_text']),
                'icon' => 'fas fa-shield-virus',
            ],
            [
                'title' => Setting::get('homepage_mission_card_2_title', $defaults['mission_card_2_title']),
                'text' => Setting::get('homepage_mission_card_2_text', $defaults['mission_card_2_text']),
                'icon' => 'fas fa-users',
            ],
            [
                'title' => Setting::get('homepage_mission_card_3_title', $defaults['mission_card_3_title']),
                'text' => Setting::get('homepage_mission_card_3_text', $defaults['mission_card_3_text']),
                'icon' => 'fas fa-network-wired',
            ],
        ];
    }

    /**
     * @return list<array{title: string, text: string, icon: string}>
     */
    private static function coreValuesCards(): array
    {
        $defaults = self::defaults();

        return [
            [
                'title' => Setting::get('homepage_core_values_card_1_title', $defaults['core_values_card_1_title']),
                'text' => Setting::get('homepage_core_values_card_1_text', $defaults['core_values_card_1_text']),
                'icon' => 'fas fa-handshake',
            ],
            [
                'title' => Setting::get('homepage_core_values_card_2_title', $defaults['core_values_card_2_title']),
                'text' => Setting::get('homepage_core_values_card_2_text', $defaults['core_values_card_2_text']),
                'icon' => 'fas fa-lightbulb',
            ],
            [
                'title' => Setting::get('homepage_core_values_card_3_title', $defaults['core_values_card_3_title']),
                'text' => Setting::get('homepage_core_values_card_3_text', $defaults['core_values_card_3_text']),
                'icon' => 'fas fa-heart',
            ],
        ];
    }

    public static function aboutDefaults(): array
    {
        return [
            'about_hero_title' => 'About Western RCC',
            'about_hero_lead' => 'The Western Regional Collaborating Centre (RCC) is part of the Africa CDC network, dedicated to strengthening health security and disease surveillance across West Africa.',
            'about_core_functions_title' => 'Core Functions',
            'about_core_functions_lead' => 'Our key areas of focus and expertise',
            'about_coverage_title' => 'Coverage Area',
            'about_coverage_lead' => 'The Western RCC serves {count} countries across West Africa, providing technical support and coordination for health security initiatives.',
            'about_function_1_title' => 'Laboratory Systems',
            'about_function_1_text' => 'Strengthening laboratory networks and diagnostic capabilities across the region.',
            'about_function_2_title' => 'Disease Surveillance',
            'about_function_2_text' => 'Advanced monitoring systems for early detection and response to health threats.',
            'about_function_3_title' => 'Emergency Response',
            'about_function_3_text' => 'Rapid response capabilities for health emergencies and disease outbreaks.',
            'about_function_4_title' => 'Training & Education',
            'about_function_4_text' => 'Capacity building programs for health professionals and institutions.',
            'about_function_5_title' => 'Partnerships',
            'about_function_5_text' => 'Building strategic partnerships with regional and international organizations.',
            'about_function_6_title' => 'Data & Analytics',
            'about_function_6_text' => 'Health data management and analysis for evidence-based decision making.',
        ];
    }

    public static function forAboutAdmin(): array
    {
        $defaults = self::aboutDefaults();

        return [
            'hero_title' => Setting::get('about_hero_title', $defaults['about_hero_title']),
            'hero_lead' => Setting::get('about_hero_lead', $defaults['about_hero_lead']),
            'core_functions_title' => Setting::get('about_core_functions_title', $defaults['about_core_functions_title']),
            'core_functions_lead' => Setting::get('about_core_functions_lead', $defaults['about_core_functions_lead']),
            'coverage_title' => Setting::get('about_coverage_title', $defaults['about_coverage_title']),
            'coverage_lead' => Setting::get('about_coverage_lead', $defaults['about_coverage_lead']),
            'core_functions' => self::coreFunctionCards(),
        ];
    }

    public static function forAboutPublic(?int $countryCount = null): array
    {
        $content = self::forAboutAdmin();

        if ($countryCount !== null) {
            $content['coverage_lead'] = str_replace('{count}', (string) $countryCount, $content['coverage_lead']);
        }

        return $content;
    }

    /**
     * @return list<array{title: string, text: string, icon: string}>
     */
    public static function coreFunctionCards(): array
    {
        $defaults = self::aboutDefaults();
        $icons = [
            'fas fa-microscope',
            'fas fa-search',
            'fas fa-shield-alt',
            'fas fa-graduation-cap',
            'fas fa-handshake',
            'fas fa-chart-line',
        ];

        $cards = [];

        for ($i = 1; $i <= 6; $i++) {
            $cards[] = [
                'title' => Setting::get("about_function_{$i}_title", $defaults["about_function_{$i}_title"]),
                'text' => Setting::get("about_function_{$i}_text", $defaults["about_function_{$i}_text"]),
                'icon' => $icons[$i - 1],
            ];
        }

        return $cards;
    }
}
