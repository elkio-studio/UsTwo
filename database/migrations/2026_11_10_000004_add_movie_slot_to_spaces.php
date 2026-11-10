<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', fn (Blueprint $table) => $table->unsignedTinyInteger('movie_slot')->nullable());
        DB::table('spaces')->update(['movie_slot' => 2]);
    }

    public function down(): void
    {
        Schema::table('spaces', fn (Blueprint $table) => $table->dropColumn('movie_slot'));
    }
};
