<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['movies', 'series', 'readings'] as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->json('catalog_fields')->nullable();
                $blueprint->unsignedInteger('metadata_version')->default(0);
            });

            $fields = $table === 'readings'
                ? ['title', 'author', 'publication_date', 'description', 'cover_url']
                : ['title', 'release_year', 'overview', 'poster_path'];
            $provider = $table === 'readings' ? 'provider' : 'tmdb_id';
            DB::table($table)->orderBy('id')->chunkById(200, function ($rows) use ($table, $fields, $provider): void {
                foreach ($rows as $row) {
                    $imported = [];
                    if ($row->{$provider} !== null) {
                        foreach ($fields as $field) {
                            $value = $row->{$field};
                            if ($value !== null && (! is_string($value) || trim($value) !== '')) {
                                $imported[] = $field;
                            }
                        }
                    }
                    DB::table($table)->where('id', $row->id)->update([
                        'catalog_fields' => json_encode($imported, JSON_THROW_ON_ERROR),
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['movies', 'series', 'readings'] as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn(['catalog_fields', 'metadata_version']));
        }
    }
};
