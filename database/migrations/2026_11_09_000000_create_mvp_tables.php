<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->date('watched_on');
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('movie_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('note')->nullable();
            $table->unique(['movie_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_ratings');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('users');
    }
};
