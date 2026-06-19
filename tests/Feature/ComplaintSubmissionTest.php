<?php

namespace Tests\Feature;

use App\Models\ComplaintCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComplaintSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('system_settings')->insert([
            'complaints_system_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_complaint_form_lists_database_categories(): void
    {
        $this->get(route('complaints.create'))
            ->assertOk()
            ->assertSee('Harassment')
            ->assertSee('Workplace Safety');
    }

    public function test_complaint_rejects_invalid_category_slug(): void
    {
        $response = $this->post(route('complaints.store'), [
            'category' => 'invalid_category',
            'description' => str_repeat('This is a detailed complaint description. ', 3),
            'is_anonymous' => '1',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_complaint_accepts_active_category_from_database(): void
    {
        ComplaintCategory::create([
            'name' => 'IT Issues',
            'slug' => 'it_issues',
            'is_active' => true,
            'sort_order' => 99,
        ]);

        $response = $this->post(route('complaints.store'), [
            'category' => 'it_issues',
            'description' => str_repeat('This is a detailed complaint description. ', 3),
            'is_anonymous' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complaints', ['category' => 'it_issues']);
    }
}
