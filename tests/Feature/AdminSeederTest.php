<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_only_the_configured_admin(): void
    {
        config()->set('admin.username', 'production-admin');
        config()->set('admin.password', 'a-secure-production-password');

        $this->seed();

        $admin = Admin::query()->sole();

        $this->assertSame('production-admin', $admin->username);
        $this->assertTrue(
            Hash::check('a-secure-production-password', $admin->password)
        );
        $this->assertDatabaseCount('bands', 0);
        $this->assertDatabaseCount('photographers', 0);
    }
}
