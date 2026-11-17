<?php

namespace Tests\Feature;

use App\Filament\Pages\Backups;
use App\Models\AdminRole;
use App\Models\User;
use App\Services\Admin\BackupService;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class BackupPageTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir().'/baharfc-backups-page-'.uniqid('', true);
        config([
            'backup.root' => $this->root,
            'backup.keep' => 4,
            'backup.retain_days' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);
        parent::tearDown();
    }

    public function test_admin_can_open_backups_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/backups')
            ->assertOk()
            ->assertSee('بک‌آپ دستی دیتابیس')
            ->assertSee('بک‌آپ دستی فایل‌ها');
    }

    public function test_staff_without_permission_cannot_open_backups_page(): void
    {
        $this->seed(AdminRoleSeeder::class);
        $author = AdminRole::query()->where('slug', 'author')->firstOrFail();
        $user = User::factory()->create([
            'is_admin' => false,
            'admin_role_id' => $author->id,
        ]);

        $this->actingAs($user)
            ->get('/admin/backups')
            ->assertForbidden();
    }

    public function test_manual_database_backup_from_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(Backups::class)
            ->call('createDatabaseBackup');

        $this->assertNotEmpty(app(BackupService::class)->list(BackupService::TYPE_DATABASE));
    }
}
