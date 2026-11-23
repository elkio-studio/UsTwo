<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', fn (Blueprint $table) => $table->unsignedTinyInteger('wish_slot')->nullable());

        if (DB::getDriverName() === 'sqlite') {
            $collision = 'NEW.wish_slot IN (NEW.movie_slot, NEW.series_slot, NEW.places_slot, NEW.reading_slot)';
            DB::unprepared("CREATE TRIGGER spaces_distinct_wish_slot_insert BEFORE INSERT ON spaces
                WHEN $collision
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_wish_slot'); END");
            DB::unprepared("CREATE TRIGGER spaces_distinct_wish_slot_update
                BEFORE UPDATE OF movie_slot, series_slot, places_slot, reading_slot, wish_slot ON spaces
                WHEN $collision
                BEGIN SELECT RAISE(ABORT, 'spaces_distinct_wish_slot'); END");
        } else {
            DB::statement('ALTER TABLE spaces ADD CONSTRAINT spaces_distinct_wish_slot
                CHECK (wish_slot NOT IN (movie_slot, series_slot, places_slot, reading_slot))');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER spaces_distinct_wish_slot_insert');
            DB::unprepared('DROP TRIGGER spaces_distinct_wish_slot_update');
        } else {
            DB::statement('ALTER TABLE spaces DROP CONSTRAINT spaces_distinct_wish_slot');
        }

        Schema::table('spaces', fn (Blueprint $table) => $table->dropColumn('wish_slot'));
    }
};
