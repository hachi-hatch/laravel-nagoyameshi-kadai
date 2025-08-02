<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    public function test_guest_cannot_access_reservation_index()
    {
        $response = $this->get('restaurants/reservations/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_reservation_index()
    {
        $user = User::factory()->create();

        $response = $this->get('restaurants/reservations/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_reservation_index()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('restaurants/reservations/index');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_reservation_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/reservations/index');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_reservation_create()
    {
        $response = $this->get('restaurants/reservations/create');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_reservation_create()
    {
        $user = User::factory()->create();

        $response = $this->get('restaurants/reservations/create');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_reservation_create()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('restaurants/reservations/create.');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_reservation_create()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/reservations/create');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_reservation_store()
    {
        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_reservation_store()
    {
         $user = User::factory()->create();

        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_access_reservation_store()
    {
        $user = User::factory()->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('restaurants.reservations.store', $restaurant), [
            'reservation_date' => now()->addDay()->format('Y-m-d'),
            'reservation_time' => '18:30',
            'number_of_people' => 3,
        ]);

        $response->assertRedirect(route('restaurants.reservations.create', $restaurant));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'number_of_people' => 3,
        ]);
    }

    public function test_admin_user_cannot_access_reservation_store()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_reservation_destroy()
    {
        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_reservation_destroy()
    {
         $user = User::factory()->create();

        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_subscribed_can_reservation_destroy()
    {
        $user = User::factory()->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $this->actingAs($user);

        $reservation = $this->post(route('restaurants.reservations.store', $restaurant), [
            'reservation_date' => now()->addDay()->format('Y-m-d'),
            'reservation_time' => '18:30',
            'number_of_people' => 3,
        ]);

        $response = $this->delete(route('reservation.destroy', $reservation));

        $response->assertRedirect(route('reservation.index'));
    }

    public function test_admin_user_cannot_reservation_destroy()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reservations');
        $response->assertRedirect('restaurants/{restaurant}');
    }
}
