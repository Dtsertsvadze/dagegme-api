<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_receive_a_token(): void
    {
        Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'admin' => ['id', 'username'],
            ]);
    }

    public function test_admin_can_access_protected_route_with_token(): void
    {
        $admin = Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/admin/me');

        $response
            ->assertOk()
            ->assertJsonPath('admin.username', 'admin');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        Admin::query()->create([
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
    }
}
