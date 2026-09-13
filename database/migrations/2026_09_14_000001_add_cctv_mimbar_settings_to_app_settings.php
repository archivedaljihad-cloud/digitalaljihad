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
            if (!Schema::hasColumn('app_settings', 'cctv_mimbar_url')) {
                $table->string('cctv_mimbar_url')->nullable()->after('live_stream_overlay');
            }
            if (!Schema::hasColumn('app_settings', 'cctv_mimbar_enabled')) {
                $table->boolean('cctv_mimbar_enabled')->default(false)->after('cctv_mimbar_url');
            }
            if (!Schema::hasColumn('app_settings', 'cctv_auto_switch_khutbah')) {
                $table->boolean('cctv_auto_switch_khutbah')->default(true)->after('cctv_mimbar_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('app_settings', 'cctv_mimbar_url')) $columns[] = 'cctv_mimbar_url';
            if (Schema::hasColumn('app_settings', 'cctv_mimbar_enabled')) $columns[] = 'cctv_mimbar_enabled';
            if (Schema::hasColumn('app_settings', 'cctv_auto_switch_khutbah')) $columns[] = 'cctv_auto_switch_khutbah';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
