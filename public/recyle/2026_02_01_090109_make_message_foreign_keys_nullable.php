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
            $table->dropForeign(['Client_idClient']);
            $table->dropForeign(['Vendeur_idVendeur']);
            $table->unsignedBigInteger('Client_idClient')->nullable()->change();
            $table->unsignedBigInteger('Vendeur_idVendeur')->nullable()->change();
            $table->foreign('Client_idClient')->references('idClient')->on('clients')->nullOnDelete();
            $table->foreign('Vendeur_idVendeur')->references('idVendeur')->on('vendeurs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['Client_idClient']);
            $table->dropForeign(['Vendeur_idVendeur']);
            $table->unsignedBigInteger('Client_idClient')->nullable(false)->change();
            $table->unsignedBigInteger('Vendeur_idVendeur')->nullable(false)->change();
            $table->foreign('Client_idClient')->references('idClient')->on('clients');
            $table->foreign('Vendeur_idVendeur')->references('idVendeur')->on('vendeurs');
        });
    }
};
