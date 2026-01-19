<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Produitcommande', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('Produit_idProduit')->index();
            $table->unsignedBigInteger('Commande_idCommande')->index();
            $table->integer('Quantite')->default(1);
            $table->double('PrixUnitaire')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('Produitcommande');
    }
};
