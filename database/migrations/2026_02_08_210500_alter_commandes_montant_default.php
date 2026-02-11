<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set a sensible default for MontantTotal to avoid inserts failing when omitted
        DB::statement("ALTER TABLE `commandes` MODIFY `MontantTotal` DOUBLE NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove default (keep column present)
        DB::statement("ALTER TABLE `commandes` MODIFY `MontantTotal` DOUBLE NOT NULL");
    }
};
