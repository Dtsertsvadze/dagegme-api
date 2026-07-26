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
}
