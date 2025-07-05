<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_company_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/company');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_company_edit()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_company_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_company_edit()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/company/edit');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_terms_index()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_terms_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_terms_index()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/terms');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_terms_edit()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_terms_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_terms_edit()
    {
        $admin = Admin::factory()->create([
        'password' => bcrypt('nagoyameshi')
    ]);

        $response = $this->actingAs($admin)->get('/admin/terms/edit');
        $response->assertOk();
    }

    public function test_guest_cannot_access_admin_terms_update()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_terms_update()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_user_can_access_terms_update()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('nagoyameshi')
        ]);

        $term = new \App\Models\Term([
            'content' => '旧利用規約の内容',
        ]);
        $term->save();

        $updateData = [
            'content' => '新しい利用規約の内容',
        ];
        $response = $this->patch(route('admin.terms.update', $term->id), $updateData);

        $this->assertDatabaseHas('terms', [
            'id' => $term->id,
            'content' => '新しい利用規約の内容',
        ]);

        $response->assertRedirect(route('admin.terms.index'));
    }
}
