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
            if (!Schema::hasColumn('app_settings', 'enable_dynamic_theme')) {
                $table->boolean('enable_dynamic_theme')->default(true)->after('rotation_pages');
            }
            if (!Schema::hasColumn('app_settings', 'enable_next_prayer_bar')) {
                $table->boolean('enable_next_prayer_bar')->default(true)->after('enable_dynamic_theme');
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
            if (Schema::hasColumn('app_settings', 'enable_dynamic_theme')) $columns[] = 'enable_dynamic_theme';
            if (Schema::hasColumn('app_settings', 'enable_next_prayer_bar')) $columns[] = 'enable_next_prayer_bar';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
