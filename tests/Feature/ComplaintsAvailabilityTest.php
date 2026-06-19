<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComplaintsAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_complaint_form_is_unavailable_when_system_disabled(): void
    {
        DB::table('system_settings')->insert([
            'complaints_system_enabled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('complaints.create'))->assertNotFound();
    }

    public function test_complaint_submission_is_rejected_when_system_disabled(): void
    {
        DB::table('system_settings')->insert([
            'complaints_system_enabled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('home'))
            ->post(route('complaints.store'), [
                'category' => 'harassment',
                'description' => str_repeat('This is a detailed complaint description. ', 3),
                'is_anonymous' => '1',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    }
}
