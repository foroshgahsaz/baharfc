<?php

namespace Tests\Feature;

use App\Livewire\Admin\MediaPickerModal;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_media_picker_event_accepts_payload_array(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(MediaPickerModal::class)
            ->dispatch('open-media-picker', [
                'statePath' => 'mountedActions.0.data.path',
                'imagesOnly' => true,
                'componentId' => 'test-component',
            ])
            ->assertSet('open', true)
            ->assertSet('targetStatePath', 'mountedActions.0.data.path')
            ->assertSet('targetComponentId', 'test-component');
    }

    public function test_modal_lists_media_and_sets_path_on_pick(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('library/test.jpg', 'x');

        $media = Media::query()->create([
            'disk' => 'public',
            'path' => 'library/test.jpg',
            'file_name' => 'test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 10,
        ]);

        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(MediaPickerModal::class)
            ->call('openPicker', 'data.image_path', true, 'test-component')
            ->assertSet('open', true)
            ->call('pick', $media->id)
            ->assertSet('open', false);
    }
}