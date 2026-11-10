<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->date('watched_on');
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->string('poster_path')->nullable();
            $table->text('overview')->nullable();
            $table->timestamps();
            $table->unique(['space_id', 'tmdb_id']);
        });

        Schema::create('series_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('note')->nullable();
            $table->unique(['series_id', 'user_id']);
        });

        Schema::table('spaces', fn (Blueprint $table) => $table->unsignedTinyInteger('series_slot')->nullable());

        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared("CREATE TRIGGER spaces_distinct_slots_insert BEFORE INSERT ON spaces
                WHEN NEW.series_slot = NEW.movie_slot
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_slots'); END");
            DB::unprepared("CREATE TRIGGER spaces_distinct_slots_update BEFORE UPDATE OF movie_slot, series_slot ON spaces
                WHEN NEW.series_slot = NEW.movie_slot
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_slots'); END");
        } else {
            DB::statement('ALTER TABLE spaces ADD CONSTRAINT spaces_distinct_slots CHECK (movie_slot <> series_slot)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER spaces_distinct_slots_insert');
            DB::unprepared('DROP TRIGGER spaces_distinct_slots_update');
        } else {
            DB::statement('ALTER TABLE spaces DROP CONSTRAINT spaces_distinct_slots');
        }

        Schema::table('spaces', fn (Blueprint $table) => $table->dropColumn('series_slot'));
        Schema::dropIfExists('series_ratings');
        Schema::dropIfExists('series');
    }
};
