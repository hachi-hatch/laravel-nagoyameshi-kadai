<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
     public function test_guest_cannot_access_admin_admin_top(): void
    {
        $response = $this->get('/admin/index');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_admin_top(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/index');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_admin_top(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/index');
        $response->assertOk();
    }
}
