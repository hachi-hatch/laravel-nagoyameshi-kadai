<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    public function test_guest_can_access_company_index()
    {
        $response = $this->get('company/index');
        $response->assertOk();
    }

    public function test_non_admin_user_can_access_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('company/index');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_favorite_index()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('company/index');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_company_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('company/index');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_access_term_index()
    {
        $response = $this->get('terms/index');
        $response->assertOk();
    }

    public function test_non_admin_user_can_access_term_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('terms/index');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_term_index()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('terms/index');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_term_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('terms/index');
        $response->assertRedirect('/login');
    }
}
