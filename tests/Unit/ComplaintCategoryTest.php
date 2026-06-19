<?php

namespace Tests\Unit;

use App\Models\ComplaintCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_uses_underscores_to_match_legacy_values(): void
    {
        $this->assertSame('workplace_safety', ComplaintCategory::makeSlug('Workplace Safety'));
    }

    public function test_unique_slug_avoids_collisions(): void
    {
        ComplaintCategory::create([
            'name' => 'IT Issues',
            'slug' => 'it_issues',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertSame('it_issues_1', ComplaintCategory::uniqueSlug('IT Issues'));
    }
}
