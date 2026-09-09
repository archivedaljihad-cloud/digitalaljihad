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
        Schema::table('sholat_jumat', function (Blueprint $table) {
            if (!Schema::hasColumn('sholat_jumat', 'foto_imam')) {
                $table->string('foto_imam')->nullable()->after('bilal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sholat_jumat', function (Blueprint $table) {
            if (Schema::hasColumn('sholat_jumat', 'foto_imam')) {
                $table->dropColumn('foto_imam');
            }
        });
    }
};
