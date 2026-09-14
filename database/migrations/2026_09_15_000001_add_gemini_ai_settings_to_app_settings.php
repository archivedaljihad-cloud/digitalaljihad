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
            if (!Schema::hasColumn('app_settings', 'gemini_api_key')) {
                $table->string('gemini_api_key')->nullable()->after('cctv_mimbar_url');
            }
            if (!Schema::hasColumn('app_settings', 'gemini_model')) {
                $table->string('gemini_model')->default('gemini-1.5-flash')->after('gemini_api_key');
            }
            if (!Schema::hasColumn('app_settings', 'daily_hikmah_cache')) {
                $table->longText('daily_hikmah_cache')->nullable()->after('gemini_model');
            }
            if (!Schema::hasColumn('app_settings', 'daily_hikmah_date')) {
                $table->date('daily_hikmah_date')->nullable()->after('daily_hikmah_cache');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if (Schema::hasColumn('app_settings', 'gemini_api_key')) {
                $table->dropColumn('gemini_api_key');
            }
            if (Schema::hasColumn('app_settings', 'gemini_model')) {
                $table->dropColumn('gemini_model');
            }
            if (Schema::hasColumn('app_settings', 'daily_hikmah_cache')) {
                $table->dropColumn('daily_hikmah_cache');
            }
            if (Schema::hasColumn('app_settings', 'daily_hikmah_date')) {
                $table->dropColumn('daily_hikmah_date');
            }
        });
    }
};
