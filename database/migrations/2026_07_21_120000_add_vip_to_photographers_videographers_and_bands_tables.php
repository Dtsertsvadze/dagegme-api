<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->boolean('vip')->default(false)->after('city');
        });

        Schema::table('videographers', function (Blueprint $table) {
            $table->boolean('vip')->default(false)->after('city');
        });

        Schema::table('bands', function (Blueprint $table) {
            $table->boolean('vip')->default(false)->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropColumn('vip');
        });

        Schema::table('videographers', function (Blueprint $table) {
            $table->dropColumn('vip');
        });

        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn('vip');
        });
    }
};
