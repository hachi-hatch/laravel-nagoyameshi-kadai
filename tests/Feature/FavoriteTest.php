<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    public function test_guest_cannot_access_favorite_index()
    {
        $response = $this->get('restaurants/favorites/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_favorite_index()
    {
        $user = User::factory()->create();

        $response = $this->get('restaurants/favorites/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_favorite_index()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('restaurants/favorites/index');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_favorite_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/favorites/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_favorite_store()
    {
        $response = $this->get('favorites.store');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_favorite_store()
    {
        $user = User::factory()->create();

        $response = $this->get('favorites.store');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_favorite_store()
    {
        $user = User::factory()->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));
        $response->aassertStatus(302);
    }

    public function test_admin_user_cannot_access_favorite_store()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('favorites.store');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_favorite_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route('favorites.destroy', $restaurant->id));
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_favorite_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', $restaurant->id));
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_favorite_destroy()
    {
        $user = User::factory()->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant->id));
        $response->aassertStatus(302);
    }

    public function test_admin_user_cannot_access_favorite_destroy()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', $restaurant->id));
        $response->assertRedirect('restaurants/{restaurant}');
    }
}
