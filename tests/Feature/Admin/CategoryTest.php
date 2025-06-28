<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_guest_cannot_access_admin_category_list(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_accsess_admin_category_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_admin_category_list(): void
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/categories');
        $response->assertOk();
    }

    public function test_guest_cannot_registration_admin_category_store(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_registration_admin_category_store(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_registration_admin_category_store(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $restaurant = Restaurant::factory()->make();

        $this->actingAs($admin, 'admin');

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'テストカテゴリ'
         ]);

         $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'テストカテゴリ']);
    }

    public function test_guest_cannot_update_admin_category_update(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_update_admin_category_update(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_update_admin_category_update(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create([
        'name' => '旧カテゴリ名'
    ]);

        $response = $this->patch(route('admin.categories.update', $category->id), [
        'name' => '新カテゴリ名'
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => '新カテゴリ名'
    ]);
        $response->assertOk();
    }

    public function test_guest_cannot_destroy_admin_category_destroy(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_destroy_admin_category_destroy(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_destroy_admin_category_destroy(): void
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $this->actingAs($admin, 'admin');

        $category = Category::factory()->create([
            'name' => 'カテゴリ名'
        ]);

        $response = $this->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name' => 'カテゴリ名',
        ]);
    }
}
