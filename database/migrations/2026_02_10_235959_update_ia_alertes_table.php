<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration uses `renameColumn`, which requires the
     * `doctrine/dbal` package to be available. If you don't have it,
     * install it with `composer require doctrine/dbal` before running migrations.
     *
     * The migration will:
     * - rename `source_type` -> `Expediteur_type`
     * - rename `source_id` -> `Expediteur_id` (if present)
     * - add `Message` TEXT nullable
     */
    public function up()
    {
        Schema::table('ia_alertes', function (Blueprint $table) {
            if (Schema::hasColumn('ia_alertes', 'source_type')) {
                $table->renameColumn('source_type', 'Expediteur_type');
            }
            if (Schema::hasColumn('ia_alertes', 'source_id')) {
                $table->renameColumn('source_id', 'Expediteur_id');
            }

            if (!Schema::hasColumn('ia_alertes', 'Expediteur_id') && !Schema::hasColumn('ia_alertes', 'source_id')) {
                // nothing to do for id rename if it did not exist
            }

            if (!Schema::hasColumn('ia_alertes', 'Message')) {
                $table->text('Message')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ia_alertes', function (Blueprint $table) {
            if (Schema::hasColumn('ia_alertes', 'Expediteur_type')) {
                $table->renameColumn('Expediteur_type', 'source_type');
            }
            if (Schema::hasColumn('ia_alertes', 'Expediteur_id')) {
                $table->renameColumn('Expediteur_id', 'source_id');
            }
            if (Schema::hasColumn('ia_alertes', 'Message')) {
                $table->dropColumn('Message');
            }
        });
    }
};
