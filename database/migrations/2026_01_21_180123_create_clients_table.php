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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('idClient');
            $table->string('Nom', 45);
            $table->string('Prenom', 45);
            $table->date('DateDeNaissance');
            $table->string('Adresse', 45);
            $table->integer('TelClient')->unique();
            $table->string('email', 45);
            $table->string('MotDePasse', 200);
            $table->timestamp('DateCreation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
