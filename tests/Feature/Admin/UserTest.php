<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_user_list(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_user_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
        // $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_user_list(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_user_detail(): void
    {
        $response = $this->get('/admin/users/{$admin->id}');

        $response->assertRedirect('admin/login');
    }
    
    public function test_non_admin_user_cannot_accsess_admin_user_detail(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users/{$admin->id}');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_user_detail(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);

        $this->assertTrue(Auth::guard('admin')->check());
        $response = $this->actingAs($admin)->get('/admin/users/{$admin->id}');
        $response->assertOk();
    }
}
