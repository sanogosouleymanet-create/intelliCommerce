<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('ia_alertes', function (Blueprint $table) {

        if (Schema::hasColumn('ia_alertes', 'Administrateur_idAdmin')) {
            $table->dropColumn('Administrateur_idAdmin');
        }

    });
}

public function down(): void
{
    Schema::table('ia_alertes', function (Blueprint $table) {

        if (!Schema::hasColumn('ia_alertes', 'Administrateur_idAdmin')) {
            $table->unsignedBigInteger('Administrateur_idAdmin');
        }

    });
}

};
