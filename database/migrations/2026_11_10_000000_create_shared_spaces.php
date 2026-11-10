<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::withoutForeignKeyConstraints(function () {
                DB::transaction(function () {
                    $this->migrateSpaces();
                    if (DB::select('PRAGMA foreign_key_check') !== []) {
                        throw new RuntimeException('La migración dejó referencias sin destino.');
                    }
                });
            });

            return;
        }

        if (app()->isProduction()) {
            DB::statement('LOCK TABLE movies, movie_ratings IN ACCESS EXCLUSIVE MODE');
            foreach (['movies', 'movie_ratings'] as $table) {
                $permissions = DB::selectOne(
                    "SELECT has_table_privilege('ustwo_app', ?, 'SELECT, INSERT, UPDATE, DELETE, TRUNCATE')
                         OR has_any_column_privilege('ustwo_app', ?, 'SELECT, INSERT, UPDATE') AS can_access",
                    [$table, $table],
                );
                if ($permissions->can_access) {
                    throw new RuntimeException('Retira primero el acceso de ustwo_app a películas y valoraciones.');
                }
            }
        }

        $this->migrateSpaces();
    }

    private function migrateSpaces(): void
    {
        $handles = ['angel@gmail.com' => 'angl_j_rgl', 'alejandra@gmail.com' => 'Aleismowgli'];
        $accounts = DB::table('users')->pluck('id', 'email');

        if (($accounts->isNotEmpty() && $accounts->keys()->sort()->values()->all() !== ['alejandra@gmail.com', 'angel@gmail.com'])
            || ($accounts->isEmpty() && DB::table('movies')->exists())) {
            throw new RuntimeException('No se puede identificar el espacio heredado. Revisa las cuentas antes de migrar.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 32)->nullable();
            $table->string('username_key', 32)->nullable()->unique();
        });
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('space_user', function (Blueprint $table) {
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['space_id', 'user_id']);
            $table->index('user_id');
        });
        Schema::create('space_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unique(['space_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
        Schema::table('movies', function (Blueprint $table) {
            $table->foreignId('space_id')->nullable()->constrained()->cascadeOnDelete();
            $table->dropUnique(['tmdb_id']);
            $table->unique(['space_id', 'tmdb_id']);
        });

        if ($accounts->isNotEmpty()) {
            $now = now();
            $space = DB::table('spaces')->insertGetId([
                'name' => 'Nuestra estantería', 'owner_id' => $accounts['angel@gmail.com'],
                'created_at' => $now, 'updated_at' => $now,
            ]);
            foreach ($handles as $email => $username) {
                DB::table('users')->where('id', $accounts[$email])->update([
                    'username' => $username, 'username_key' => strtolower($username),
                ]);
                DB::table('space_user')->insert(['space_id' => $space, 'user_id' => $accounts[$email]]);
            }
            DB::table('movies')->update(['space_id' => $space]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 32)->change();
            $table->string('username_key', 32)->change();
        });
        Schema::table('movies', function (Blueprint $table) {
            $table->unsignedBigInteger('space_id')->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('La reversión del esquema requiere restaurar una copia previa.');
    }
};
