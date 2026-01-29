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
        Schema::create('produits', function (Blueprint $table) {
        $table->id('idProduit'); // AUTO_INCREMENT

        $table->string('Nom', 45);
        $table->string('Description', 45);
        $table->double('Prix');
        $table->integer('Stock');
        $table->string('Categorie', 45);
        $table->timestamp('DateAjout'); // TIMESTAMP
        $table->string('Image', 255);

        $table->unsignedBigInteger('Vendeur_idVendeur');

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
        Schema::dropIfExists('produits');
    }
};
