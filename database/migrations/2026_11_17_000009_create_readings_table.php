<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('category', 12);
            $table->string('title');
            $table->text('title_key');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('publication_date', 10)->nullable();
            $table->text('cover_url')->nullable();
            $table->string('provider', 30)->nullable();
            $table->string('record_type', 20)->nullable();
            $table->string('external_id', 80)->nullable();
            $table->timestamps();
            $table->unique(['space_id', 'provider', 'record_type', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
