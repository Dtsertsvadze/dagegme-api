<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Band;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProviderTranslationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_returns_all_provider_translations(): void
    {
        $band = Band::query()->create([
            'name' => ['en' => 'Wedding Band', 'ka' => 'საქორწილო ბენდი'],
            'description' => ['en' => 'Live music', 'ka' => 'ცოცხალი მუსიკა'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ]);

        $this->getJson("/api/bands/{$band->id}")
            ->assertOk()
            ->assertJsonPath('name.en', 'Wedding Band')
            ->assertJsonPath('name.ka', 'საქორწილო ბენდი')
            ->assertJsonPath('description.en', 'Live music')
            ->assertJsonPath('description.ka', 'ცოცხალი მუსიკა')
            ->assertJsonPath('city.en', 'Tbilisi')
            ->assertJsonPath('city.ka', 'თბილისი');
    }

    public function test_admin_requests_require_both_language_keys(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $this->postJson('/api/admin/bands', [
            'name' => ['en' => 'Wedding Band'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name.ka');
    }

    public function test_djs_and_rental_cars_accept_and_return_translated_descriptions(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $description = [
            'en' => 'Available for weddings',
            'ka' => 'ხელმისაწვდომია ქორწილებისთვის',
        ];

        $this->postJson('/api/admin/djs', [
            'name' => ['en' => 'DJ Alex', 'ka' => 'დიჯეი ალექსი'],
            'description' => $description,
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ])
            ->assertCreated()
            ->assertJsonPath('description.en', $description['en'])
            ->assertJsonPath('description.ka', $description['ka']);

        $this->postJson('/api/admin/rental-cars', [
            'model' => 'E-Class',
            'mark' => 'Mercedes-Benz',
            'year' => 2024,
            'description' => $description,
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ])
            ->assertCreated()
            ->assertJsonPath('description.en', $description['en'])
            ->assertJsonPath('description.ka', $description['ka']);
    }

    public function test_dj_and_rental_car_descriptions_require_both_languages(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $this->postJson('/api/admin/djs', [
            'name' => ['en' => 'DJ Alex', 'ka' => 'დიჯეი ალექსი'],
            'description' => ['en' => 'Available for weddings'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('description.ka');

        $this->postJson('/api/admin/rental-cars', [
            'model' => 'E-Class',
            'mark' => 'Mercedes-Benz',
            'year' => 2024,
            'description' => ['ka' => 'ხელმისაწვდომია ქორწილებისთვის'],
            'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('description.en');
    }
}
