<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', fn(Blueprint $table) => $table->unsignedTinyInteger('places_slot')->nullable());

        if (DB::getDriverName() === 'sqlite') {
            $collision = 'NEW.places_slot = NEW.movie_slot OR NEW.places_slot = NEW.series_slot';
            DB::unprepared("CREATE TRIGGER spaces_distinct_places_slot_insert BEFORE INSERT ON spaces
                WHEN $collision
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_places_slot'); END");
            DB::unprepared("CREATE TRIGGER spaces_distinct_places_slot_update BEFORE UPDATE OF movie_slot, series_slot, places_slot ON spaces
                WHEN $collision
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_places_slot'); END");
        } else {
            DB::statement('ALTER TABLE spaces ADD CONSTRAINT spaces_distinct_places_slot
                CHECK (places_slot <> movie_slot AND places_slot <> series_slot)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER spaces_distinct_places_slot_insert');
            DB::unprepared('DROP TRIGGER spaces_distinct_places_slot_update');
        } else {
            DB::statement('ALTER TABLE spaces DROP CONSTRAINT spaces_distinct_places_slot');
        }

        Schema::table('spaces', fn(Blueprint $table) => $table->dropColumn('places_slot'));
    }
};
