<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'Administrateur_idAdministrateur')) {
                $table->unsignedBigInteger('Administrateur_idAdministrateur')->nullable()->after('Vendeur_idVendeur');
                $table->foreign('Administrateur_idAdministrateur')
                      ->references('idAdmi')
                      ->on('administrateurs')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'Administrateur_idAdministrateur')) {
                $table->dropForeign(['Administrateur_idAdministrateur']);
                $table->dropColumn('Administrateur_idAdministrateur');
            }
        });
    }
};
