<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $username = env('ADMIN_USERNAME');
        $password = env('ADMIN_PASSWORD');

        if (! $username || ! $password) {
            return;
        }

        Admin::query()->updateOrCreate(
            ['username' => $username],
            ['password' => $password],
        );
    }
}
