<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $username = config('admin.username');
        $password = config('admin.password');

        if (! $username || ! $password) {
            throw new RuntimeException(
                'ADMIN_USERNAME and ADMIN_PASSWORD must be configured before seeding the admin.'
            );
        }

        Admin::query()->updateOrCreate(
            ['username' => $username],
            ['password' => $password],
        );
    }
}
