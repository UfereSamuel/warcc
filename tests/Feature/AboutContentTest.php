<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithWarccAdmin;
use Tests\TestCase;

class AboutContentTest extends TestCase
{
    use InteractsWithWarccAdmin;
    use RefreshDatabase;

    public function test_public_about_page_shows_default_cms_content(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('About Western RCC')
            ->assertSee('Core Functions')
            ->assertSee('Laboratory Systems');
    }

    public function test_admin_can_load_about_content_editor(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.content.about'))
            ->assertOk()
            ->assertSee('About Page Content');
    }

    public function test_admin_can_update_about_page_content(): void
    {
        $this->actingAsSuperAdmin();

        $payload = [
            'hero_title' => 'Updated About Title',
            'hero_lead' => 'Updated lead text for the about page.',
            'core_functions_title' => 'Our Functions',
            'core_functions_lead' => 'What we do across the region.',
            'coverage_title' => 'Regional Coverage',
            'coverage_lead' => 'Serving {count} countries in West Africa.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $payload["function_{$i}_title"] = "Function {$i}";
            $payload["function_{$i}_text"] = "Description for function {$i}.";
        }

        $this->put(route('admin.content.about.update'), $payload)
            ->assertRedirect(route('admin.content.about'));

        $this->assertSame('Updated About Title', Setting::get('about_hero_title'));

        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('Updated About Title')
            ->assertSee('Function 1');
    }
}
