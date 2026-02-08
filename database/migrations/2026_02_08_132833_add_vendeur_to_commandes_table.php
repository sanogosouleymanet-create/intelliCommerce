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
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('Vendeur_idVendeur')->nullable()->after('Client_idClient');
            $table->foreign('Vendeur_idVendeur')->references('idVendeur')->on('vendeurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['Vendeur_idVendeur']);
            $table->dropColumn('Vendeur_idVendeur');
        });
    }
};
