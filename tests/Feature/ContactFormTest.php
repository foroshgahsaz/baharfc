<?php

namespace Tests\Feature;

use App\Enums\ContactMessageStatus;
use App\Livewire\Shop\ContactForm;
use App\Models\ContactMessage;
use App\Models\ShopSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ShopSetting::query()->create([
            'contact_form_enabled' => true,
            'contact_form_show_phone' => true,
            'contact_form_show_subject' => true,
        ]);
    }

    public function test_contact_page_and_submission(): void
    {
        $this->get(route('contact'))->assertOk()->assertSee('تماس با ما');

        Livewire::test(ContactForm::class)
            ->set('name', 'کاربر')
            ->set('email', 'user@example.com')
            ->set('subject', 'سوال')
            ->set('message', 'این یک پیام تستی برای پشتیبانی است.')
            ->call('submit')
            ->assertSet('statusMessage', 'پیام شما ثبت شد. به زودی با شما تماس می‌گیریم.');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'user@example.com',
            'status' => ContactMessageStatus::New->value,
        ]);
    }

    public function test_contact_page_hidden_when_disabled(): void
    {
        ShopSetting::query()->first()?->update(['contact_form_enabled' => false]);

        $this->get(route('contact'))->assertNotFound();
    }

    public function test_admin_can_open_contact_inbox(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        ContactMessage::query()->create([
            'name' => 'ن',
            'email' => 'a@b.com',
            'message' => 'سلام تست',
            'status' => ContactMessageStatus::New,
        ]);

        $this->actingAs($admin)
            ->get('/admin/contact-messages')
            ->assertOk()
            ->assertSee('a@b.com');
    }
}