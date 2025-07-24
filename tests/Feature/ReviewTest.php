<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    public function test_guest_cannot_access_review_index()
    {
        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_can_access_review_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_review_index()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_review_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_review_create()
    {
        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_can_access_review_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_review_create()
    {
        $user = User::factory()->create('pm_card_visa');

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews');
        $response->assertOk();
    }

    public function test_admin_user_cannot_access_subscription_create()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_review_store()
    {
        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_review_store()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_review_store()
    {
        $user = User::factory()->create('pm_card_visa');
        $review = Review::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $reviewData = [
            'restaurant_id' => $restaurant->id,
            'score' => 5,
            'comment' => '素晴らしいレストランでした！',
        ];

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'restaurant_id' => $review->restaurant_id,
            'score' => $review->score,
            'comment' => $review->comment,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'restaurant_id' => $review->restaurant_id,
            'user_id' => $user->id,
            'score' => $review->score,
            'comment' => $review->comment,
        ]);
    }

    public function test_admin_user_cannot_access_subscription_store()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reviews');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_access_review_edit()
    {
        $response = $this->get('restaurants/{restaurant}/reviews/edit');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_non_admin_user_cannot_access_review_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('restaurants/{restaurant}/reviews/edit');
        $response->assertOk();
    }

    public function test_non_admin_user_subscribed_can_access_review_edit()
    {
        $user = User::factory()->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);

        $url = route('reviews.edit', ['restaurant' => $restaurant->id, 'review' => $review->id]);

        $response = $this->actingAs($user)->get($url);

        $response->assertOk();
    }

    public function test_admin_user_cannot_access_subscription_edit()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->get('restaurants/{restaurant}/reviews/edit');
        $response->assertRedirect('restaurants/{restaurant}');
    }

    public function test_guest_cannot_update_review()
    {
        $review = Review::factory()->create();
        $restaurant = $review->restaurant;

        $response = $this->patch(route('reviews.update', [
            'restaurant' => $restaurant->id,
            'review' => $review->id
        ]), [
            'score' => 4,
            'comment' => 'ゲストが更新しようとした',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_update_review()
    {
        $user = User::factory()->create();
        $review = Reciew::factory()->create();
        $restaurant = $review->restaurtant;

        $response = $this->patch(route('review.update', [
            'restaurant' => $restaurant->id,
            'review' => $review->id
        ]), [
            'score' => 4,
            'comment' => '無料ユーザーが更新しようとした',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_subscribed_user_cannot_update_others_review()
    {
        $user = User::factory()->create();
        $user->createAsStripeCustomer();
        $user->newSubscription('premium_plan', 'price_xxx')->create('pm_card_visa');

        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->patch(route('reviews.update', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id
        ]), [
            'score' => 5,
            'comment' => '他人のレビューを更新しようとした',
        ]);

        $response->assertStatus(403);
    }

    public function test_subscribed_user_can_update_their_own_review()
    {
        $user = User::factory()->create();
        $user->createAsStripeCustomer();
        $user->newSubscription('premium_plan', 'price_xxx')->create('pm_card_visa');

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'score' => 3,
            'comment' => '古いコメント',
        ]);

        $response = $this->actingAs($user)->patch(route('reviews.update', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id
        ]), [
            'score' => 5,
            'comment' => 'とてもよかった',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'score' => 5,
            'comment' => 'とてもよかった',
        ]);
    }

    public function test_admin_cannot_update_review()
    {
        $admin = Admin::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($admin, 'admin')->patch(route('reviews.update', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id
        ]), [
            'score' => 2,
            'comment' => '管理者がレビューを変更しようとした',
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_delete_review()
    {
        $review = Review::factory()->create();

        $response = $this->delete(route('reviews.destroy', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id,
        ]));

        $response->assertRedirect('/login');
    }

    public function test_free_user_cannot_delete_review()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id,
        ]));

        $response->assertRedirect('/subscribe');
    }

    public function test_subscribed_user_cannot_delete_others_review()
    {
        $user = User::factory()->create();
        $user->createAsStripeCustomer();
        $user->newSubscription('premium_plan', 'price_xxx')->create('pm_card_visa');

        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id,
        ]));

        $response->assertStatus(403);
    }

    public function test_subscribed_user_can_delete_their_review()
    {
        $user = User::factory()->create();
        $user->createAsStripeCustomer();
        $user->newSubscription('premium_plan', 'price_xxx')->create('pm_card_visa');

        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id,
        ]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_admin_cannot_delete_review()
    {
        $admin = Admin::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('reviews.destroy', [
            'restaurant' => $review->restaurant_id,
            'review' => $review->id,
        ]));

        $response->assertStatus(403);
    }
}
