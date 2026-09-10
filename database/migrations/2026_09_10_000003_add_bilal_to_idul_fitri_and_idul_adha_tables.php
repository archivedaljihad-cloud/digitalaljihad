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
        Schema::table('sholat_idul_fitri', function (Blueprint $table) {
            if (!Schema::hasColumn('sholat_idul_fitri', 'bilal')) {
                $table->string('bilal')->nullable()->after('muadzin');
            }
        });

        Schema::table('sholat_idul_adha', function (Blueprint $table) {
            if (!Schema::hasColumn('sholat_idul_adha', 'bilal')) {
                $table->string('bilal')->nullable()->after('muadzin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sholat_idul_fitri', function (Blueprint $table) {
            if (Schema::hasColumn('sholat_idul_fitri', 'bilal')) {
                $table->dropColumn('bilal');
            }
        });

        Schema::table('sholat_idul_adha', function (Blueprint $table) {
            if (Schema::hasColumn('sholat_idul_adha', 'bilal')) {
                $table->dropColumn('bilal');
            }
        });
    }
};
