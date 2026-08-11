<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('djs', function (Blueprint $table): void {
            $table->json('description')->nullable();
        });

        Schema::table('rental_cars', function (Blueprint $table): void {
            $table->json('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('djs', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('rental_cars', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }
};
