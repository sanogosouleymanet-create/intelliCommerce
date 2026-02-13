<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ia_alertes', 'source_type')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->string('source_type')->nullable()->after('destinataire_id');
            });
        }

        if (!Schema::hasColumn('ia_alertes', 'source_id')) {
            Schema::table('ia_alertes', function (Blueprint $table) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ia_alertes', function (Blueprint $table) {
            if (Schema::hasColumn('ia_alertes', 'source_type')) {
                $table->dropColumn('source_type');
            }
            if (Schema::hasColumn('ia_alertes', 'source_id')) {
                $table->dropColumn('source_id');
            }
        });
    }
};
