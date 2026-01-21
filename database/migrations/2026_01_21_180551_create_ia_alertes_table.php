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
        Schema::create('ia_alertes', function (Blueprint $table) {
            $table->id('idAlerte');
            $table->string('TypeAlerte', 45);
            $table->string('Description', 500);
            $table->timestamp('DateCreation');
            $table->string('NiveauGravité', 45);
            $table->unsignedBigInteger('Administrateur_idAdmi');

            $table->foreign('Administrateur_idAdmi')
                  ->references('idAdmi')
                  ->on('administrateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ia_alertes');
    }
};
