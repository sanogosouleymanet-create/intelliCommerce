<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer ancienne colonne admin si elle existe
        if (Schema::hasColumn('ia_alertes', 'Administrateur_idAdmi')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->dropForeign(['Administrateur_idAdmi']);
                $table->dropColumn('Administrateur_idAdmi');
            });
        }

        // Ajouter destinataire_type si absent
        if (!Schema::hasColumn('ia_alertes', 'destinataire_type')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->string('destinataire_type')
                      ->after('NiveauGravité');
            });
        }

        // Ajouter destinataire_id si absent
        if (!Schema::hasColumn('ia_alertes', 'destinataire_id')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->unsignedBigInteger('destinataire_id')
                      ->after('destinataire_type');
            });
        }

        // Ajouter lu si absent
        if (!Schema::hasColumn('ia_alertes', 'lu')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->boolean('lu')
                      ->default(false);
            });
        }
    }

    public function down(): void
    {
        // Supprimer nouvelles colonnes si elles existent
        Schema::table('ia_alertes', function (Blueprint $table) {

            if (Schema::hasColumn('ia_alertes', 'destinataire_type')) {
                $table->dropColumn('destinataire_type');
            }

            if (Schema::hasColumn('ia_alertes', 'destinataire_id')) {
                $table->dropColumn('destinataire_id');
            }

            if (Schema::hasColumn('ia_alertes', 'lu')) {
                $table->dropColumn('lu');
            }

            // Restaurer ancienne colonne si nécessaire
            if (!Schema::hasColumn('ia_alertes', 'Administrateur_idAdmi')) {
                $table->unsignedBigInteger('Administrateur_idAdmi');
                $table->foreign('Administrateur_idAdmi')
                      ->references('idAdmi')
                      ->on('administrateurs');
            }
        });
    }
};
