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
            // Add a nullable destinataire (recipient) for vendeur-to-vendeur messages
            if (!Schema::hasColumn('messages', 'VendeurDestinataire_idVendeur')) {
                $table->unsignedBigInteger('VendeurDestinataire_idVendeur')->nullable()->after('Vendeur_idVendeur');
                $table->foreign('VendeurDestinataire_idVendeur')
                      ->references('idVendeur')
                      ->on('vendeurs')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'VendeurDestinataire_idVendeur')) {
                $table->dropForeign(['VendeurDestinataire_idVendeur']);
                $table->dropColumn('VendeurDestinataire_idVendeur');
            }
        });
    }
};
