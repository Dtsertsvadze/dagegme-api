<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\RentalCar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RentalCarPhotosTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_rental_car_with_gallery_photos(): void
    {
        config(['media.disk' => 'media']);
        Storage::fake('media');
        $this->actingAsAdmin();

        $response = $this->post('/api/admin/rental-cars', [
            'model' => 'E-Class',
            'mark' => 'Mercedes-Benz',
            'year' => 2024,
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
            'photos' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('interior.jpg'),
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonCount(2, 'photos')
            ->assertJsonStructure([
                'profile_photo',
                'profile_photo_url',
                'photos' => [
                    ['id', 'rental_car_id', 'photo_path', 'photo_url'],
                ],
            ]);

        $this->assertDatabaseCount('rental_car_photos', 2);
        Storage::disk('media')->assertExists($response->json('photos.0.photo_path'));
        Storage::disk('media')->assertExists($response->json('photos.1.photo_path'));

        $this->getJson('/api/rental-cars/'.$response->json('id'))
            ->assertOk()
            ->assertJsonCount(2, 'photos');
    }

    public function test_admin_can_append_or_replace_rental_car_photos(): void
    {
        $this->actingAsAdmin();
        $rentalCar = $this->createRentalCar();
        $existingPhoto = $rentalCar->photos()->create([
            'photo_path' => 'https://example.com/existing.jpg',
        ]);

        $this->putJson("/api/admin/rental-cars/{$rentalCar->id}", [
            'photos' => ['https://example.com/appended.jpg'],
        ])
            ->assertOk()
            ->assertJsonCount(2, 'photos');

        $this->assertDatabaseHas('rental_car_photos', [
            'id' => $existingPhoto->id,
        ]);

        $this->putJson("/api/admin/rental-cars/{$rentalCar->id}", [
            'photos' => ['https://example.com/replacement.jpg'],
            'replace_photos' => true,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'photos')
            ->assertJsonPath('photos.0.photo_path', 'https://example.com/replacement.jpg');

        $this->assertDatabaseMissing('rental_car_photos', [
            'id' => $existingPhoto->id,
        ]);
    }

    public function test_admin_can_delete_one_photo_from_its_rental_car(): void
    {
        config(['media.disk' => 'media']);
        Storage::fake('media');
        $this->actingAsAdmin();

        $rentalCar = $this->createRentalCar();
        $deletedPhoto = $rentalCar->photos()->create([
            'photo_path' => 'rental-cars/photos/delete-me.jpg',
        ]);
        $remainingPhoto = $rentalCar->photos()->create([
            'photo_path' => 'rental-cars/photos/keep-me.jpg',
        ]);
        Storage::disk('media')->put($deletedPhoto->photo_path, 'photo');
        Storage::disk('media')->put($remainingPhoto->photo_path, 'photo');

        $this->deleteJson(
            "/api/admin/rental-cars/{$rentalCar->id}/photos/{$deletedPhoto->id}"
        )
            ->assertOk()
            ->assertJsonPath('deleted_photo_id', $deletedPhoto->id);

        $this->assertDatabaseMissing('rental_car_photos', [
            'id' => $deletedPhoto->id,
        ]);
        $this->assertDatabaseHas('rental_car_photos', [
            'id' => $remainingPhoto->id,
        ]);
        Storage::disk('media')->assertMissing($deletedPhoto->photo_path);
        Storage::disk('media')->assertExists($remainingPhoto->photo_path);
    }

    public function test_photo_must_belong_to_the_rental_car_in_the_url(): void
    {
        $this->actingAsAdmin();
        $rentalCar = $this->createRentalCar();
        $otherRentalCar = $this->createRentalCar('S-Class');
        $photo = $otherRentalCar->photos()->create([
            'photo_path' => 'rental-cars/photos/other.jpg',
        ]);

        $this->deleteJson(
            "/api/admin/rental-cars/{$rentalCar->id}/photos/{$photo->id}"
        )->assertNotFound();

        $this->assertDatabaseHas('rental_car_photos', [
            'id' => $photo->id,
        ]);
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));
    }

    private function createRentalCar(string $model = 'E-Class'): RentalCar
    {
        return RentalCar::query()->create([
            'model' => $model,
            'mark' => 'Mercedes-Benz',
            'year' => 2024,
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ]);
    }
}
