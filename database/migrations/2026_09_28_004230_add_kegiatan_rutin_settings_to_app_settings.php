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
        Schema::table('app_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('app_settings', 'kegiatan_rutin_settings')) {
                $table->longText('kegiatan_rutin_settings')->nullable()->after('yasin_scroll_speed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if (Schema::hasColumn('app_settings', 'kegiatan_rutin_settings')) {
                $table->dropColumn('kegiatan_rutin_settings');
            }
        });
    }
};
