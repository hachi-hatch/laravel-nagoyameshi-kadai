<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get('/user/index');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_can_access_user_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/index');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_user_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('/user/index');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_user_edit()
    {
        $response = $this->get('/user/edit');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_other_user_edit()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($userA)->get('/edit');
        
        $response = $this->get(route('user.edit', ['user' => $userB->id]));

        $response->assertRedirect(route('user.index'));
    }

    public function test_non_admin_user_can_access_user_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/edit');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_user_edit()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('/user/edit');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_user_update()
    {
        $response = $this->get('/user/edit');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_other_user_update()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($userA)->get('/update');
        
        $response = $this->get(route('user.update', ['user' => $userB->id]));

        $response->assertRedirect(route('user.index'));
    }

    public function test_non_admin_user_can_access_user_update()
    {
        $user = User::factory()->create([
            'name' => '旧名前',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user);

        $updatedData = [
            'name' => '新しい名前',
            'email' => 'new@example.com',
        ];

        $response = $this->patch(route('user.update', ['user' => $user->id]), $updatedData);

        $response->assertRedirect(route('user.index'));
    }

    public function test_admin_user_cannot_access_user_update()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('/user/edit');
        $response->assertRedirect('/');
    }
}
