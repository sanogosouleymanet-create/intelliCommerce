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
        // Make existing foreign key columns nullable and add admin column
        // Use raw statements to avoid requiring doctrine/dbal for change()
        DB::statement("ALTER TABLE `messages` MODIFY `Client_idClient` bigint unsigned NULL");
        DB::statement("ALTER TABLE `messages` MODIFY `Vendeur_idVendeur` bigint unsigned NULL");

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'Administrateur_idAdministrateur')) {
                $table->unsignedBigInteger('Administrateur_idAdministrateur')->nullable()->after('Vendeur_idVendeur');
            }
        });

        // add foreign key for administrateur if table exists
        try {
            DB::statement(
                "ALTER TABLE `messages` ADD CONSTRAINT `messages_administrateur_fk` FOREIGN KEY (`Administrateur_idAdministrateur`) REFERENCES `administrateurs`(`idAdmi`) ON DELETE SET NULL"
            );
        } catch (\Throwable $e) {
            // ignore if FK cannot be added
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Try to drop the FK and column, and make client/vendor not null again
        try {
            DB::statement("ALTER TABLE `messages` DROP FOREIGN KEY `messages_administrateur_fk`");
        } catch (\Throwable $e) {}

        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'Administrateur_idAdministrateur')) {
                $table->dropColumn('Administrateur_idAdministrateur');
            }
        });

        DB::statement("ALTER TABLE `messages` MODIFY `Client_idClient` bigint unsigned NOT NULL");
        DB::statement("ALTER TABLE `messages` MODIFY `Vendeur_idVendeur` bigint unsigned NOT NULL");
    }
};
