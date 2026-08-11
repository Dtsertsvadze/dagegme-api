<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('djs', function (Blueprint $table): void {
            $table->boolean('vip')->default(false)->after('city');
        });

        Schema::table('halls', function (Blueprint $table): void {
            $table->boolean('vip')->default(false)->after('city');
        });

        Schema::table('presenters', function (Blueprint $table): void {
            $table->boolean('vip')->default(false)->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('djs', function (Blueprint $table): void {
            $table->dropColumn('vip');
        });

        Schema::table('halls', function (Blueprint $table): void {
            $table->dropColumn('vip');
        });

        Schema::table('presenters', function (Blueprint $table): void {
            $table->dropColumn('vip');
        });
    }
};
