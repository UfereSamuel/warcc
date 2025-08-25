<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'WARCC Staff Management System',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your website that appears in the title and header',
                'sort_order' => 1,
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Africa CDC Regional Collaborating Centre',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Tagline',
                'description' => 'A short description that appears with your site name',
                'sort_order' => 2,
            ],
            [
                'key' => 'site_description',
                'value' => 'Comprehensive staff management system for the Africa CDC Regional Collaborating Centre.',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Site Description',
                'description' => 'A detailed description of your website for SEO purposes',
                'sort_order' => 3,
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'label' => 'Site Logo',
                'description' => 'Upload your organization logo (recommended size: 200x60px)',
                'sort_order' => 4,
            ],

            // Contact Information
            [
                'key' => 'contact_organization',
                'value' => 'Africa CDC Regional Collaborating Centre',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Organization Name',
                'description' => 'Official name of your organization',
                'sort_order' => 1,
            ],
            [
                'key' => 'contact_address',
                'value' => 'University of Ghana\nLegon, Accra\nGhana',
                'type' => 'textarea',
                'group' => 'contact',
                'label' => 'Physical Address',
                'description' => 'Complete physical address of your organization',
                'sort_order' => 2,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+233 XX XXX XXXX',
                'type' => 'phone',
                'group' => 'contact',
                'label' => 'Phone Number',
                'description' => 'Primary phone number for contact',
                'sort_order' => 3,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@africacdc.org',
                'type' => 'email',
                'group' => 'contact',
                'label' => 'Email Address',
                'description' => 'Primary email address for contact',
                'sort_order' => 4,
            ],
            [
                'key' => 'contact_website',
                'value' => 'https://africacdc.org',
                'type' => 'url',
                'group' => 'contact',
                'label' => 'Website URL',
                'description' => 'Official website URL',
                'sort_order' => 5,
            ],
            [
                'key' => 'contact_fax',
                'value' => '',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Fax Number',
                'description' => 'Fax number (optional)',
                'sort_order' => 6,
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Complete Facebook page URL',
                'sort_order' => 1,
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Complete Twitter profile URL',
                'sort_order' => 2,
            ],
            [
                'key' => 'social_linkedin',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'LinkedIn URL',
                'description' => 'Complete LinkedIn page URL',
                'sort_order' => 3,
            ],
            [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Complete Instagram profile URL',
                'sort_order' => 4,
            ],
            [
                'key' => 'social_youtube',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'label' => 'YouTube URL',
                'description' => 'Complete YouTube channel URL',
                'sort_order' => 5,
            ],

            // System Settings
            [
                'key' => 'timezone',
                'value' => 'Africa/Accra',
                'type' => 'select',
                'group' => 'system',
                'label' => 'Default Timezone',
                'description' => 'Default timezone for the system',
                'sort_order' => 1,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'select',
                'group' => 'system',
                'label' => 'Date Format',
                'description' => 'Default date format used throughout the system',
                'sort_order' => 2,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Maintenance Mode',
                'description' => 'Enable maintenance mode to show a maintenance page to visitors',
                'sort_order' => 3,
            ],

            // YouTube/Media Settings
            [
                'key' => 'youtube_channel_id',
                'value' => '',
                'type' => 'text',
                'group' => 'media',
                'label' => 'YouTube Channel ID',
                'description' => 'Your YouTube Channel ID (e.g., UC1234567890abcdef). Found in YouTube Studio > Settings > Channel > Advanced',
                'sort_order' => 1,
            ],
            [
                'key' => 'youtube_channel_url',
                'value' => '',
                'type' => 'url',
                'group' => 'media',
                'label' => 'YouTube Channel URL',
                'description' => 'Complete YouTube channel URL (e.g., https://www.youtube.com/@africacdc)',
                'sort_order' => 2,
            ],
            [
                'key' => 'youtube_api_key',
                'value' => '',
                'type' => 'text',
                'group' => 'media',
                'label' => 'YouTube API Key',
                'description' => 'YouTube Data API v3 key for fetching channel content. Get from Google Cloud Console.',
                'sort_order' => 3,
            ],
            [
                'key' => 'youtube_embed_channel',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'media',
                'label' => 'Enable YouTube Integration',
                'description' => 'Show YouTube channel content on the website',
                'sort_order' => 4,
            ],
            [
                'key' => 'youtube_homepage_videos',
                'value' => '6',
                'type' => 'number',
                'group' => 'media',
                'label' => 'Homepage Videos Count',
                'description' => 'Number of latest videos to show on homepage (0 to disable)',
                'sort_order' => 5,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
