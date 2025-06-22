<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    public function test_guest_cannot_access_admin_restaurant_list(): void
    {
        $response = $this->get('/admin/index');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_restaurant_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/index');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_list(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/index');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_restaurant_show(): void
    {
        $response = $this->get('/admin/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_restaurant_show(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/edit');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_show(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/edit');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_restaurant_create(): void
    {
        $response = $this->get('/admin/create');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_restaurant_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/create');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_restaurant_create(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/create');
        $response->assertOk();
    }

    public function test_guest_cannot_registration_admin_restaurant_store(): void
    {
        $response = $this->get('/admin/create');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_registration_admin_restaurant_store(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/create');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_registration_admin_restaurant_store(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $restaurant = Restaurant::factory()->make();

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.restaurants.store'), $restaurant->toArray());
        $response->assertRedirect(route('admin.restaurants.index'));

        $this->assertDatabaseHas('restaurants', [
        'name' => $restaurant->name,
        'address' => $restaurant->address,
    ]);
    }

    public function test_guest_cannot_accsess_admin_restaurant_edit(): void
    {
        $response = $this->get('/admin/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_restaurant_edit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/edit');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_accsess_admin_restaurant_edit(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $response = $this->actingAs($admin)->get('/admin/edit');
        $response->assertOk();
    }

    public function test_guest_cannot_update_admin_restaurant_update(): void
    {
        $response = $this->get('/admin/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_update_admin_restaurant_update(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/edit');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_update_admin_restaurant_update(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $response = $this->actingAs($admin)->get('/admin/edit');
        $response->assertOk();
    }

    public function test_guest_cannot_destroy_admin_restaurant_destroy(): void
    {
        $response = $this->get('/admin/destroy');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_destroy_admin_restaurant_destroy(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/destroy');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_destroy_admin_restaurant_destroy(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $restaurant = Restaurant::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.restaurants.show', $restaurant->id));
        $response->assertOk();
        $response->assertSee($restaurant->name);
        
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant->id));

        $response->assertRedirect(route('admin.restaurants.index'));

        $this->assertDatabaseMissing('restaurants', [
        'id' => $restaurant->id,
    ]);
    }
}
