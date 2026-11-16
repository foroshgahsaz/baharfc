<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminRoleSeeder::class);
    }

    public function test_author_can_access_blog_but_not_orders(): void
    {
        $authorRole = AdminRole::query()->where('slug', 'author')->firstOrFail();

        $author = User::factory()->create([
            'is_admin' => false,
            'admin_role_id' => $authorRole->id,
            'password' => 'secret-pass',
        ]);

        $this->assertTrue($author->canAdmin('blog_posts.create'));
        $this->assertFalse($author->canAdmin('orders.view'));

        $this->actingAs($author)
            ->get('/admin/blog-posts')
            ->assertOk();

        $this->actingAs($author)
            ->get('/admin/orders')
            ->assertForbidden();
    }

    public function test_shop_manager_can_access_orders_but_not_blog_posts(): void
    {
        $managerRole = AdminRole::query()->where('slug', 'shop_manager')->firstOrFail();

        $manager = User::factory()->create([
            'is_admin' => false,
            'admin_role_id' => $managerRole->id,
            'password' => 'secret-pass',
        ]);

        $this->actingAs($manager)
            ->get('/admin/orders')
            ->assertOk();

        $this->actingAs($manager)
            ->get('/admin/blog-posts')
            ->assertForbidden();
    }

    public function test_super_admin_still_has_full_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($admin->canAdmin('orders.delete'));
        $this->assertTrue($admin->canAdmin('roles.create'));

        $this->actingAs($admin)
            ->get('/admin/admin-roles')
            ->assertOk();
    }
}