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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id('idCommande');
            $table->timestamp('DateCommande');
            $table->string('Statut', 45)->default('en cours');
            $table->double('MontanTotal');
            $table->unsignedBigInteger('Client_idClient');

            $table->foreign('Client_idClient')
                  ->references('idClient')
                  ->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
