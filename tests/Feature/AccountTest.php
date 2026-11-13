<?php

namespace Tests\Feature;

use App\Livewire\Account\AccountIndex;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_routes_require_authentication(): void
    {
        $this->get(route('account.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_account_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('account.index'))
            ->assertOk()
            ->assertSeeLivewire(AccountIndex::class);
    }

    public function test_user_can_update_profile_via_livewire(): void
    {
        $user = User::factory()->create(['name' => 'قدیم', 'is_admin' => false]);

        Livewire::actingAs($user)
            ->test(AccountIndex::class)
            ->set('name', 'جدید')
            ->call('saveProfile')
            ->assertHasNoErrors();

        $this->assertSame('جدید', $user->fresh()->name);
    }

    public function test_user_can_create_address(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(AccountIndex::class)
            ->set('activeTab', 'addresses')
            ->call('openCreateAddress')
            ->set('addressTitle', 'منزل')
            ->set('recipientName', 'علی رضایی')
            ->set('addressPhone', '09121234567')
            ->set('province', 'تهران')
            ->set('city', 'تهران')
            ->set('addressLine', 'خیابان آزادی')
            ->set('isDefault', true)
            ->call('saveAddress')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'title' => 'منزل',
            'is_default' => true,
        ]);
    }

    public function test_user_cannot_delete_another_users_address(): void
    {
        $owner = User::factory()->create(['is_admin' => false]);
        $other = User::factory()->create(['is_admin' => false]);
        $address = Address::query()->create([
            'user_id' => $owner->id,
            'title' => 'x',
            'recipient_name' => 'a',
            'phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'addr',
        ]);

        Livewire::actingAs($other)
            ->test(AccountIndex::class)
            ->call('deleteAddress', $address->id);

        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }
}
