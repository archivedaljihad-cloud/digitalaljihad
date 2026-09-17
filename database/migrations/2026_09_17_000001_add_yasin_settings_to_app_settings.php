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
            if (!Schema::hasColumn('app_settings', 'yasin_mode_enabled')) {
                $table->boolean('yasin_mode_enabled')->default(true)->after('running_text_pages');
            }
            if (!Schema::hasColumn('app_settings', 'yasin_start_time')) {
                $table->string('yasin_start_time', 10)->default('18:30')->after('yasin_mode_enabled');
            }
            if (!Schema::hasColumn('app_settings', 'yasin_scroll_speed')) {
                $table->string('yasin_scroll_speed', 20)->default('medium')->after('yasin_start_time');
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
            if (Schema::hasColumn('app_settings', 'yasin_mode_enabled')) $columns[] = 'yasin_mode_enabled';
            if (Schema::hasColumn('app_settings', 'yasin_start_time')) $columns[] = 'yasin_start_time';
            if (Schema::hasColumn('app_settings', 'yasin_scroll_speed')) $columns[] = 'yasin_scroll_speed';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
