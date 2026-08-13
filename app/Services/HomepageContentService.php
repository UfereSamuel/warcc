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
            'organization_mission_text' => 'To strengthen health security and disease surveillance capacity across West Africa, advancing Africa CDC’s Africa Health Security and Sovereignty (AHSS) Agenda through partnerships, technical assistance, and support to Member States.',
            'organization_vision_title' => 'Our Vision',
            'organization_vision_text' => 'A West Africa region with sovereign, resilient health systems — able to prevent, detect, and respond to outbreaks while financing, producing, and governing more of its own health solutions.',
            'mission_title' => 'How Western RCC supports Member States',
            'mission_description' => 'Regional coordination that helps West African countries deliver on continental health security priorities under the AHSS Agenda.',
            'mission_card_1_title' => 'Surveillance & PPPR',
            'mission_card_1_text' => 'Strengthening prevention, preparedness, and response systems so threats are detected early and managed quickly.',
            'mission_card_2_title' => 'Workforce & institutions',
            'mission_card_2_text' => 'Building national and regional capacity in public health institutes, laboratories, and emergency workforces.',
            'mission_card_3_title' => 'Data & coordination',
            'mission_card_3_text' => 'Improving trusted data flows and cross-border collaboration for evidence-based health security decisions.',
            'serving_title' => 'Serving West Africa',
            'serving_description' => 'The Western RCC works with {count} AU Member States across West Africa to advance health security and sovereignty.',
            'featured_events_title' => 'Featured Events',
            'featured_events_description' => 'Upcoming opportunities to strengthen health security and sovereignty across West Africa',
            'core_values_title' => 'Our Core Values',
            'core_values_description' => 'Guiding principles for delivering the AHSS Agenda in West Africa',
            'core_values_card_1_title' => 'Sovereignty',
            'core_values_card_1_text' => 'Supporting African-led solutions that reduce dependency and strengthen regional ownership of health systems.',
            'core_values_card_2_title' => 'Solidarity',
            'core_values_card_2_text' => 'Working across borders so West African countries prevent and respond to health threats together.',
            'core_values_card_3_title' => 'Science & excellence',
            'core_values_card_3_text' => 'Grounding action in evidence, strong institutions, and high standards of public health practice.',
            'ahss_title' => 'Africa Health Security and Sovereignty (AHSS)',
            'ahss_lead' => 'Western RCC aligns its work with Africa CDC’s AHSS Agenda — Africa’s path from health dependency toward greater autonomy, preparedness, and self-reliance.',
            'ahss_link_label' => 'Learn about AHSS on Africa CDC',
            'ahss_link_url' => 'https://africacdc.org/africas-health-security-sovereignty-agenda/',
            'ahss_pillar_1_title' => 'Reformed global health architecture',
            'ahss_pillar_1_text' => 'Stronger African voice and leadership in global health decision-making.',
            'ahss_pillar_2_title' => 'Institutionalised PPPR',
            'ahss_pillar_2_text' => 'Continental prevention, preparedness, and response systems that detect and respond faster.',
            'ahss_pillar_3_title' => 'Sustainable health financing',
            'ahss_pillar_3_text' => 'Predictable domestic, innovative, and blended financing for health security.',
            'ahss_pillar_4_title' => 'Digital transformation & data sovereignty',
            'ahss_pillar_4_text' => 'Trusted, African-owned data and digital intelligence from communities to continental platforms.',
            'ahss_pillar_5_title' => 'Local manufacturing',
            'ahss_pillar_5_text' => 'Expanding African production of vaccines, diagnostics, therapeutics, and essential supplies.',
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
            'ahss_title' => Setting::get('homepage_ahss_title', $defaults['ahss_title']),
            'ahss_lead' => Setting::get('homepage_ahss_lead', $defaults['ahss_lead']),
            'ahss_link_label' => Setting::get('homepage_ahss_link_label', $defaults['ahss_link_label']),
            'ahss_link_url' => Setting::get('homepage_ahss_link_url', $defaults['ahss_link_url']),
            'ahss_pillars' => self::ahssPillars(),
        ];
    }

    public static function forPublic(?int $countryCount = null): array
    {
        $content = self::forAdmin();
        $content['images'] = self::homepageImages();

        if ($countryCount !== null) {
            $content['serving_description'] = str_replace('{count}', (string) $countryCount, $content['serving_description']);
        }

        return $content;
    }

    /**
     * Admin-managed homepage images.
     *
     * @return array{mission_image: ?string, gallery: list<array{image: ?string, caption: string, url: ?string}>}
     */
    public static function homepageImages(): array
    {
        $gallery = [];

        for ($i = 1; $i <= 3; $i++) {
            $path = Setting::get("homepage_gallery_{$i}_image")
                ?: "images/homepage/placeholder_gallery_{$i}.png";
            $gallery[] = [
                'image' => $path,
                'caption' => (string) Setting::get("homepage_gallery_{$i}_caption", ''),
                'url' => self::mediaUrl($path),
            ];
        }

        $missionPath = Setting::get('homepage_mission_image')
            ?: 'images/homepage/placeholder_mission_vision.png';

        return [
            'mission_image' => $missionPath,
            'mission_image_url' => self::mediaUrl($missionPath),
            'gallery' => $gallery,
        ];
    }

    /**
     * Resolve a stored media path (public asset or storage upload) to a URL.
     */
    public static function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return str_starts_with($path, 'http') ? $path : asset($path);
        }

        return asset('storage/'.$path);
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
     * @return list<array{number: int, title: string, text: string}>
     */
    private static function ahssPillars(): array
    {
        $defaults = self::defaults();
        $pillars = [];

        for ($i = 1; $i <= 5; $i++) {
            $pillars[] = [
                'number' => $i,
                'title' => Setting::get("homepage_ahss_pillar_{$i}_title", $defaults["ahss_pillar_{$i}_title"]),
                'text' => Setting::get("homepage_ahss_pillar_{$i}_text", $defaults["ahss_pillar_{$i}_text"]),
            ];
        }

        return $pillars;
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
            'about_hero_lead' => 'The Western Regional Collaborating Centre is part of the Africa CDC network, supporting West African Member States to advance the Africa Health Security and Sovereignty (AHSS) Agenda.',
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
