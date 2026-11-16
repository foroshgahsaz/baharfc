<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeployResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_ok_when_menus_migration_not_applied_yet(): void
    {
        if (Schema::hasTable('menus')) {
            Schema::dropIfExists('menu_items');
            Schema::dropIfExists('menus');
        }

        $this->assertFalse(Schema::hasTable('menus'));

        $this->get(route('home'))->assertOk();
        $this->get(route('products.index'))->assertOk();
    }

    public function test_admin_menus_page_does_not_500_without_menus_table(): void
    {
        if (Schema::hasTable('menus')) {
            Schema::dropIfExists('menu_items');
            Schema::dropIfExists('menus');
        }

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/menus')
            ->assertForbidden();
    }
}