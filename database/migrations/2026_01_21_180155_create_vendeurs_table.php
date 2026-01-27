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
        Schema::create('vendeurs', function (Blueprint $table) {
            $table->id('idVendeur');
            $table->string('Nom', 45);
            $table->string('Prenom', 45);
            $table->string('Adresse', 45);
            $table->integer('TelVendeur')->unique();
            $table->string('email', 45);
            $table->string('NomBoutique', 45)->unique();
            $table->string('Statut', 20)->default('actif'); // par défaut
            $table->string('MotDePasse', 200);
            $table->timestamp('DateCreation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendeurs');
    }
};
