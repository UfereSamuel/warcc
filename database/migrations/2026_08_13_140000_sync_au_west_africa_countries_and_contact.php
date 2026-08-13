<?php

use App\Models\Country;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sync countries to AU Western Africa membership and correct RCC contact defaults.
     */
    public function up(): void
    {
        $countries = [
            ['name' => 'Benin', 'official_name' => 'The Republic of Benin', 'flag_code' => 'bj', 'sort_order' => 1],
            ['name' => 'Burkina Faso', 'official_name' => 'Burkina Faso', 'flag_code' => 'bf', 'sort_order' => 2],
            ['name' => 'Cabo Verde', 'official_name' => 'The Republic of Cabo Verde', 'flag_code' => 'cv', 'sort_order' => 3],
            ['name' => 'Côte d\'Ivoire', 'official_name' => 'The Republic of Côte d\'Ivoire', 'flag_code' => 'ci', 'sort_order' => 4],
            ['name' => 'Gambia', 'official_name' => 'The Republic of The Gambia', 'flag_code' => 'gm', 'sort_order' => 5],
            ['name' => 'Ghana', 'official_name' => 'The Republic of Ghana', 'flag_code' => 'gh', 'sort_order' => 6],
            ['name' => 'Guinea', 'official_name' => 'The Republic of Guinea', 'flag_code' => 'gn', 'sort_order' => 7],
            ['name' => 'Guinea-Bissau', 'official_name' => 'The Republic of Guinea-Bissau', 'flag_code' => 'gw', 'sort_order' => 8],
            ['name' => 'Liberia', 'official_name' => 'The Republic of Liberia', 'flag_code' => 'lr', 'sort_order' => 9],
            ['name' => 'Mali', 'official_name' => 'The Republic of Mali', 'flag_code' => 'ml', 'sort_order' => 10],
            ['name' => 'Niger', 'official_name' => 'The Republic of Niger', 'flag_code' => 'ne', 'sort_order' => 11],
            ['name' => 'Nigeria', 'official_name' => 'The Federal Republic of Nigeria', 'flag_code' => 'ng', 'sort_order' => 12],
            ['name' => 'Senegal', 'official_name' => 'The Republic of Senegal', 'flag_code' => 'sn', 'sort_order' => 13],
            ['name' => 'Sierra Leone', 'official_name' => 'The Republic of Sierra Leone', 'flag_code' => 'sl', 'sort_order' => 14],
            ['name' => 'Togo', 'official_name' => 'The Togolese Republic', 'flag_code' => 'tg', 'sort_order' => 15],
        ];

        if (Schema::hasTable('countries')) {
            $keepFlagCodes = collect($countries)->pluck('flag_code')->all();

            // Mauritania is not in AU Western Africa membership for WARCC display.
            Country::query()
                ->whereNotIn('flag_code', $keepFlagCodes)
                ->update(['is_active' => false]);

            foreach ($countries as $country) {
                $existing = Country::query()
                    ->where('flag_code', $country['flag_code'])
                    ->first();

                // Legacy short name for Cabo Verde
                if (! $existing && $country['flag_code'] === 'cv') {
                    $existing = Country::query()->where('name', 'Cape Verde')->first();
                }

                if (! $existing) {
                    $existing = Country::query()->where('name', $country['name'])->first();
                }

                if ($existing) {
                    $existing->update([
                        'name' => $country['name'],
                        'official_name' => $country['official_name'],
                        'flag_code' => $country['flag_code'],
                        'is_active' => true,
                        'sort_order' => $country['sort_order'],
                    ]);
                } else {
                    Country::create([
                        ...$country,
                        'is_active' => true,
                    ]);
                }
            }
        }

        if (Schema::hasTable('settings')) {
            $this->upsertSetting('contact_address', "Plot 114 Yakubu Gowon Cres, Asokoro\nAso 900103, Federal Capital Territory\nNigeria", 'textarea', 'Physical Address', 2);
            $this->upsertSetting('contact_email', 'westernrcc@africacdc.org', 'email', 'Email Address', 4);
            $this->upsertSetting(
                'contact_map_embed_url',
                'https://maps.google.com/maps?q=Plot+114+Yakubu+Gowon+Cres,+Asokoro,+Abuja,+Nigeria&output=embed',
                'url',
                'Google Maps Embed URL',
                7
            );
        }
    }

    public function down(): void
    {
        // Non-destructive: leave contact and country data as-is.
    }

    private function upsertSetting(string $key, string $value, string $type, string $label, int $sortOrder): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => 'contact',
                'label' => $label,
                'description' => $label,
                'sort_order' => $sortOrder,
            ]
        );
    }
};
