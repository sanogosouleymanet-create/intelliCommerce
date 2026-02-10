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
    Schema::create('mots_interdits', function (Blueprint $table) {
        $table->id('idMot');
        $table->string('mot');
        $table->integer('poids')->default(1); // poids = niveau de gravité
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mots_interdits');
    }
};
