<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change 'lu' (boolean) to 'Statut' (string: 'lu' or 'non lu')
     */
    public function up(): void
    {
        // Check if 'lu' column exists and 'Statut' doesn't exist yet
        if (Schema::hasColumn('ia_alertes', 'lu') && !Schema::hasColumn('ia_alertes', 'Statut')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                // Add new Statut column
                $table->string('Statut', 20)->nullable()->after('lu');
            });

            // Migrate existing data: lu=true -> Statut='lu', lu=false -> Statut='non lu'
            DB::table('ia_alertes')->update([
                'Statut' => DB::raw("CASE WHEN lu = 1 THEN 'lu' ELSE 'non lu' END")
            ]);

            // Make Statut not nullable after migration
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->string('Statut', 20)->nullable(false)->change();
            });

            // Drop old 'lu' column
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->dropColumn('lu');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ia_alertes', 'Statut') && !Schema::hasColumn('ia_alertes', 'lu')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                // Add back 'lu' column
                $table->boolean('lu')->default(false)->after('Statut');
            });

            // Migrate data back: Statut='lu' -> lu=true, otherwise lu=false
            DB::table('ia_alertes')->update([
                'lu' => DB::raw("CASE WHEN Statut = 'lu' THEN 1 ELSE 0 END")
            ]);

            // Drop 'Statut' column
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->dropColumn('Statut');
            });
        }
    }
};
