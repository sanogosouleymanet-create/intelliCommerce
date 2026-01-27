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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('idMessage');
            $table->string('Contenu', 500);
            $table->timestamp('DateEnvoi');
            $table->string('Statut', 45)->default('non lu');
            $table->unsignedBigInteger('Client_idClient');
            $table->unsignedBigInteger('Vendeur_idVendeur');

            $table->foreign('Client_idClient')
                  ->references('idClient')
                  ->on('clients');

            $table->foreign('Vendeur_idVendeur')
                  ->references('idVendeur')
                  ->on('vendeurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
