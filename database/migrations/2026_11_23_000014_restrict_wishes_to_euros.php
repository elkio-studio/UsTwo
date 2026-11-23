<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('wishes')->whereNotNull('currency')->where('currency', '!=', 'EUR')->exists()) {
            throw new RuntimeException('Hay deseos en otra moneda. Revisarlos antes de restringir a euros; no se convierten ni borran datos.');
        }
        if (DB::getDriverName() === 'sqlite') {
            foreach (['INSERT', 'UPDATE'] as $operation) {
                DB::unprepared('CREATE TRIGGER wishes_eur_only_'.strtolower($operation)." BEFORE $operation ON wishes
                    WHEN NEW.currency IS NOT NULL AND NEW.currency != 'EUR'
                    BEGIN SELECT RAISE(ABORT, 'wishes_eur_only'); END");
            }
        } else {
            DB::statement("ALTER TABLE wishes ADD CONSTRAINT wishes_eur_only CHECK (currency IS NULL OR currency = 'EUR')");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER wishes_eur_only_insert');
            DB::unprepared('DROP TRIGGER wishes_eur_only_update');
        } else {
            DB::statement('ALTER TABLE wishes DROP CONSTRAINT wishes_eur_only');
        }
    }
};
