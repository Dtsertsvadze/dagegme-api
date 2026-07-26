<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Photographer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PhotographerPhotoDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_one_photographer_photo(): void
    {
        config(['media.disk' => 'media']);
        Storage::fake('media');

        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $photographer = $this->createPhotographer();
        $deletedPhoto = $photographer->photos()->create([
            'photo_path' => 'photographers/photos/delete-me.jpg',
        ]);
        $remainingPhoto = $photographer->photos()->create([
            'photo_path' => 'photographers/photos/keep-me.jpg',
        ]);

        Storage::disk('media')->put($deletedPhoto->photo_path, 'photo');
        Storage::disk('media')->put($remainingPhoto->photo_path, 'photo');

        $this->deleteJson(
            "/api/admin/photographers/{$photographer->id}/photos/{$deletedPhoto->id}"
        )
            ->assertOk()
            ->assertJsonPath('deleted_photo_id', $deletedPhoto->id);

        $this->assertDatabaseMissing('photographer_photos', [
            'id' => $deletedPhoto->id,
        ]);
        $this->assertDatabaseHas('photographer_photos', [
            'id' => $remainingPhoto->id,
        ]);
        Storage::disk('media')->assertMissing($deletedPhoto->photo_path);
        Storage::disk('media')->assertExists($remainingPhoto->photo_path);
    }

    public function test_photo_must_belong_to_the_photographer_in_the_url(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $photographer = $this->createPhotographer();
        $otherPhotographer = $this->createPhotographer('Other Photographer');
        $photo = $otherPhotographer->photos()->create([
            'photo_path' => 'photographers/photos/other.jpg',
        ]);

        $this->deleteJson(
            "/api/admin/photographers/{$photographer->id}/photos/{$photo->id}"
        )->assertNotFound();

        $this->assertDatabaseHas('photographer_photos', [
            'id' => $photo->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_delete_a_photo(): void
    {
        $photographer = $this->createPhotographer();
        $photo = $photographer->photos()->create([
            'photo_path' => 'photographers/photos/protected.jpg',
        ]);

        $this->deleteJson(
            "/api/admin/photographers/{$photographer->id}/photos/{$photo->id}"
        )->assertUnauthorized();

        $this->assertDatabaseHas('photographer_photos', [
            'id' => $photo->id,
        ]);
    }

    private function createPhotographer(string $name = 'Photographer'): Photographer
    {
        return Photographer::query()->create([
            'name' => ['en' => $name, 'ka' => $name],
            'description' => ['en' => 'Description', 'ka' => 'Description'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'Tbilisi'],
        ]);
    }
}
