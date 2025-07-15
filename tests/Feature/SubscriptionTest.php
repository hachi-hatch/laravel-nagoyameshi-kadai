<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Admin;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_guest_cannot_access_subscription_create()
    {
        $response = $this->get('subscription/create');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_can_access_subscription_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('subscription/create');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_cannot_access_subscription_create()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->get('subscription/create');
        $response->assertRedirect('subscription/edit');
    }

    public function test_admin_user_cannot_access_subscription_create()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('subscription/create');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_subscription_store()
    {
        $response = $this->get('subscription/store');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_can_access_subscription_store()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('subscription/store');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_cannot_access_subscription_store()
    {
        $user = User::factory()->create();
        $user->createAsStripeCustomer();
        $user->newSubscription('premium_plan', 'price_1RjxUjRXD0aIuuSQuSf2QBDW')
            ->create('pm_card_visa');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->postroute(('subscription.store'), $request_parameter);

        $response = $this->get('subscription/store');
        $response->assertRedirect('subscription/edit');
    }

    public function test_admin_user_cannot_access_subscription_store()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('subscription/store');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_subscription_edit()
    {
        $response = $this->get('subscription/edit');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_subscription_edit()
    {
        $user = User::factory()->create();

        $$response = $this->get('subscription/create');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_subscribed_can_access_subscription_edit()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->get('subscription/edit');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_subscription_edit()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('subscription/create');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_subscription_cancel()
    {
        $response = $this->get('subscription/cancel');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_subscription_cancel()
    {
        $user = User::factory()->create();

        $$response = $this->get('subscription/cancel');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_subscribed_can_access_subscription_cancel()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->get('subscription/cancel');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_subscription_cancel()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('subscription/cancel');
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_subscription_destroy()
    {
        $response = $this->get('subscription/destroy');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_cannot_access_subscription_destroy()
    {
        $user = User::factory()->create();

        $response = $this->get('subscription/destroy');
        $response->assertRedirect('/');
    }

    public function test_non_admin_user_subscribed_can_access_subscription_destroy()
    {
        $user = User::factory()->create('pm_card_visa');

        $user->newSubscription('premium_plan', 'price_1RjxUjRXD0aIuuSQuSf2QBDW')
             ->create('pm_card_visa');

        $this->assertTrue($user->subscribed('premium_plan'));

        $this->actingAs($user);

        $respomse = $this->delete(route('subscription.dlete'));

        $user->refresh();

        $this->assertFalse($user->subscribed('premium_plan'));
    }

    public function test_admin_user_cannot_access_subscription_destroy()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('subscription/destroy');
        $response->assertRedirect('/');
    }
}
