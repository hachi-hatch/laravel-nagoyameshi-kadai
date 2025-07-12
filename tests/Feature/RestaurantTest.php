<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    public function test_guest_can_access_restaurant_index()
    {
        $response = $this->get('/');
        $response->assertOk();
    }

    public function test_non_admin_user_can_access_restaurant_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_restaurant_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect('admin/login');
    }
}
