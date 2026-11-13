<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('emoji', 32)->nullable();
            $table->timestamps();
        });

        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_list_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('latitude', 17, 14);
            $table->decimal('longitude', 17, 14);
            $table->text('note')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
        Schema::dropIfExists('place_lists');
    }
};
