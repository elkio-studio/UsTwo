<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_ratings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reading_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('note')->nullable();
            $table->unique(['reading_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_ratings');
    }
};
