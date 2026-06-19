<?php

namespace Tests\Unit;

use App\Services\HomepageContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageContentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_defaults_include_six_core_functions(): void
    {
        $defaults = HomepageContentService::aboutDefaults();

        $this->assertSame('About Western RCC', $defaults['about_hero_title']);

        for ($i = 1; $i <= 6; $i++) {
            $this->assertArrayHasKey("about_function_{$i}_title", $defaults);
            $this->assertArrayHasKey("about_function_{$i}_text", $defaults);
        }
    }

    public function test_for_about_public_replaces_country_count_placeholder(): void
    {
        $content = HomepageContentService::forAboutPublic(15);

        $this->assertStringContainsString('15', $content['coverage_lead']);
        $this->assertStringNotContainsString('{count}', $content['coverage_lead']);
    }

    public function test_core_function_cards_return_six_entries_with_icons(): void
    {
        $cards = HomepageContentService::coreFunctionCards();

        $this->assertCount(6, $cards);
        $this->assertArrayHasKey('title', $cards[0]);
        $this->assertArrayHasKey('text', $cards[0]);
        $this->assertArrayHasKey('icon', $cards[0]);
    }
}
