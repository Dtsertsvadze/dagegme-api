<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MediaStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_photographer_photos_use_the_configured_media_disk(): void
    {
        config(['media.disk' => 'media']);
        Storage::fake('media');

        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $response = $this->post('/api/admin/photographers', [
            'name' => ['en' => 'Photographer', 'ka' => 'ფოტოგრაფი'],
            'description' => ['en' => 'Description', 'ka' => 'აღწერა'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
            'photos' => [
                UploadedFile::fake()->image('gallery.jpg'),
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'profile_photo',
                'profile_photo_url',
                'photos' => [
                    ['photo_path', 'photo_url'],
                ],
            ]);

        Storage::disk('media')->assertExists(
            $response->json('profile_photo')
        );
        Storage::disk('media')->assertExists(
            $response->json('photos.0.photo_path')
        );
    }
}
