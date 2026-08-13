<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepagePublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_fifteen_au_west_africa_countries_without_mauritania(): void
    {
        $this->seed(SettingsSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('The Republic of Benin', false)
            ->assertSee('The Republic of Cabo Verde', false)
            ->assertSee('The Federal Republic of Nigeria', false)
            ->assertSee('The Togolese Republic', false)
            ->assertDontSee('Mauritania', false)
            ->assertDontSee('Islamic Republic of Mauritania', false);

        $this->assertSame(15, Country::active()->count());
    }

    public function test_homepage_and_contact_show_nigeria_address_and_official_email(): void
    {
        $this->seed(SettingsSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('westernrcc@africacdc.org', false)
            ->assertSee('Asokoro', false)
            ->assertSee('Federal Capital Territory', false)
            ->assertDontSee('University of Ghana', false)
            ->assertDontSee('Legon, Accra', false);

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('westernrcc@africacdc.org', false)
            ->assertSee('Plot 114 Yakubu Gowon Cres', false)
            ->assertSee('Asokoro', false)
            ->assertDontSee('University of Ghana', false);
    }

    public function test_contact_address_and_email_remain_admin_updatable_settings(): void
    {
        $this->seed(SettingsSeeder::class);

        Setting::set('contact_address', "Custom RCC Address\nAbuja");
        Setting::set('contact_email', 'custom@africacdc.org');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Custom RCC Address', false)
            ->assertSee('custom@africacdc.org', false);
    }
}
