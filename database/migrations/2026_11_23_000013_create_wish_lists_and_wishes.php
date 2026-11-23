<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wish_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wish_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->string('url', 2048);
            $table->string('title');
            $table->text('description')->nullable();
            $table->bigInteger('price_minor')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        $valid = "(price_minor IS NULL AND currency IS NULL) OR
            (price_minor IS NOT NULL AND currency IS NOT NULL AND price_minor BETWEEN 0 AND 9999999999
                AND price_minor = CAST(price_minor AS BIGINT) AND currency IN ('EUR', 'USD', 'GBP'))";
        if (DB::getDriverName() === 'sqlite') {
            $newValid = str_replace(['price_minor', 'currency'], ['NEW.price_minor', 'NEW.currency'], $valid);
            foreach (['INSERT', 'UPDATE'] as $operation) {
                DB::unprepared('CREATE TRIGGER wishes_valid_price_'.strtolower($operation)." BEFORE $operation ON wishes
                    WHEN NOT ($newValid) BEGIN SELECT RAISE(ABORT, 'wishes_valid_price'); END");
            }
        } else {
            DB::statement("ALTER TABLE wishes ADD CONSTRAINT wishes_valid_price CHECK ($valid)");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wishes');
        Schema::dropIfExists('wish_lists');
    }
};
