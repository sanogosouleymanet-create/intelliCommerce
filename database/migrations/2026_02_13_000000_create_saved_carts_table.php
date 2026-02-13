<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Panier', function (Blueprint $table) {
            $table->id();
            $table->string('guard')->index();
            $table->string('user_id')->index();
            $table->json('cart')->nullable();
            $table->timestamps();
            $table->unique(['guard', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Panier');
    }
};
