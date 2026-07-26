<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, array<string, bool>>
     */
    private array $translatedFields = [
        'bands' => ['name' => false, 'description' => true, 'city' => false],
        'djs' => ['name' => false, 'city' => false],
        'halls' => ['name' => false, 'description' => true, 'city' => false],
        'photographers' => ['name' => false, 'description' => true, 'city' => false],
        'presenters' => ['name' => false, 'description' => true, 'city' => false],
        'rental_cars' => ['city' => false],
        'videographers' => ['name' => false, 'description' => true, 'city' => false],
    ];

    public function up(): void
    {
        foreach ($this->translatedFields as $table => $fields) {
            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach (array_keys($fields) as $field) {
                    $blueprint->json("{$field}_translations")->nullable();
                }
            });

            DB::table($table)
                ->select(['id', ...array_keys($fields)])
                ->orderBy('id')
                ->each(function (object $record) use ($table, $fields): void {
                    $translations = [];

                    foreach ($fields as $field => $nullable) {
                        $value = $record->{$field};
                        $translations["{$field}_translations"] = $value === null && $nullable
                            ? null
                            : json_encode(
                                ['en' => $value, 'ka' => $value],
                                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                            );
                    }

                    DB::table($table)->where('id', $record->id)->update($translations);
                });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                $blueprint->dropColumn(array_keys($fields));
            });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach (array_keys($fields) as $field) {
                    $blueprint->renameColumn("{$field}_translations", $field);
                }
            });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach ($fields as $field => $nullable) {
                    $blueprint->json($field)->nullable($nullable)->change();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->translatedFields as $table => $fields) {
            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach ($fields as $field => $nullable) {
                    $column = $field === 'description'
                        ? $blueprint->text("{$field}_original")
                        : $blueprint->string("{$field}_original");

                    $column->nullable();
                }
            });

            DB::table($table)
                ->select(['id', ...array_keys($fields)])
                ->orderBy('id')
                ->each(function (object $record) use ($table, $fields): void {
                    $originals = [];

                    foreach ($fields as $field => $nullable) {
                        $translations = json_decode($record->{$field} ?? 'null', true);
                        $originals["{$field}_original"] = $translations === null && $nullable
                            ? null
                            : ($translations['en'] ?? $translations['ka'] ?? '');
                    }

                    DB::table($table)->where('id', $record->id)->update($originals);
                });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                $blueprint->dropColumn(array_keys($fields));
            });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach (array_keys($fields) as $field) {
                    $blueprint->renameColumn("{$field}_original", $field);
                }
            });

            Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
                foreach ($fields as $field => $nullable) {
                    $column = $field === 'description'
                        ? $blueprint->text($field)
                        : $blueprint->string($field);

                    $column->nullable($nullable)->change();
                }
            });
        }
    }
};
