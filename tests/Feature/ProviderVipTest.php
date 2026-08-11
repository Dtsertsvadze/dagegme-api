<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProviderVipTest extends TestCase
{
    use RefreshDatabase;

    public function test_djs_halls_and_presenters_support_vip_status(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $providers = [
            'djs' => [
                'name' => ['en' => 'DJ Alex', 'ka' => 'დიჯეი ალექსი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
            'halls' => [
                'name' => ['en' => 'Grand Hall', 'ka' => 'გრანდ ჰოლი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
            'presenters' => [
                'name' => ['en' => 'Alex Smith', 'ka' => 'ალექს სმითი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
        ];

        foreach ($providers as $resource => $payload) {
            $response = $this->postJson("/api/admin/{$resource}", [
                ...$payload,
                'vip' => true,
            ])
                ->assertCreated()
                ->assertJsonPath('vip', true);

            $this->getJson("/api/{$resource}/{$response->json('id')}")
                ->assertOk()
                ->assertJsonPath('vip', true);

            $this->putJson("/api/admin/{$resource}/{$response->json('id')}", [
                'vip' => false,
            ])
                ->assertOk()
                ->assertJsonPath('vip', false);
        }
    }

    public function test_vip_defaults_to_false_for_djs_halls_and_presenters(): void
    {
        Sanctum::actingAs(Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]));

        $providers = [
            'djs' => [
                'name' => ['en' => 'DJ Alex', 'ka' => 'დიჯეი ალექსი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
            'halls' => [
                'name' => ['en' => 'Grand Hall', 'ka' => 'გრანდ ჰოლი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
            'presenters' => [
                'name' => ['en' => 'Alex Smith', 'ka' => 'ალექს სმითი'],
                'city' => ['en' => 'Tbilisi', 'ka' => 'თბილისი'],
            ],
        ];

        foreach ($providers as $resource => $payload) {
            $response = $this->postJson("/api/admin/{$resource}", $payload)
                ->assertCreated();

            $this->getJson("/api/{$resource}/{$response->json('id')}")
                ->assertOk()
                ->assertJsonPath('vip', false);
        }
    }
}
