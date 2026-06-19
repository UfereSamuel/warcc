<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class WebsiteSettingsTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_settings_page_auto_seeds_default_fields(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Site Name')
            ->assertSee('Site Tagline')
            ->assertSee('Contact Information')
            ->assertSee('Where to edit what');
    }

    public function test_settings_page_shows_contact_fields_on_contact_tab(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('contact_email')
            ->assertSee('Organization Name');
    }
}
