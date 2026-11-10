<?php

use App\Models\Space;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
        });

        foreach (DB::table('spaces')->lazyById() as $space) {
            DB::table('spaces')->where('id', $space->id)->update(['slug' => Space::availableSlug($space->name)]);
        }
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
