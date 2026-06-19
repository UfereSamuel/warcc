<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_about_page_loads(): void
    {
        $this->get(route('public.about'))->assertOk();
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('public.contact'))->assertOk();
    }
}
