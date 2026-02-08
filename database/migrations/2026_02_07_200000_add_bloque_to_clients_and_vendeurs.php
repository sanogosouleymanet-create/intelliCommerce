<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (!Schema::hasColumn('clients', 'Bloque')) {
                    $table->boolean('Bloque')->default(false)->after('DateCreation');
                }
            });
        }

        if (Schema::hasTable('vendeurs')) {
            Schema::table('vendeurs', function (Blueprint $table) {
                if (!Schema::hasColumn('vendeurs', 'Bloque')) {
                    $table->boolean('Bloque')->default(false)->after('DateCreation');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('clients')) {
            Schema::table('clients', function (Blueprint $table) {
                if (Schema::hasColumn('clients', 'Bloque')) {
                    $table->dropColumn('Bloque');
                }
            });
        }

        if (Schema::hasTable('vendeurs')) {
            Schema::table('vendeurs', function (Blueprint $table) {
                if (Schema::hasColumn('vendeurs', 'Bloque')) {
                    $table->dropColumn('Bloque');
                }
            });
        }
    }
};
