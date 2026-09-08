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
            if (!Schema::hasColumn('app_settings', 'tarhim_trigger_seconds')) {
                $table->integer('tarhim_trigger_seconds')->nullable()->default(300);
            }
            if (!Schema::hasColumn('app_settings', 'tarhim_audio')) {
                $table->string('tarhim_audio')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'tarhim_audio_subuh')) {
                $table->string('tarhim_audio_subuh')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'tarhim_audio_reguler')) {
                $table->string('tarhim_audio_reguler')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'prayer_bg_image')) {
                $table->string('prayer_bg_image')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'prayer_bg_opacity')) {
                $table->integer('prayer_bg_opacity')->nullable()->default(80);
            }
            if (!Schema::hasColumn('app_settings', 'msg_countdown')) {
                $table->text('msg_countdown')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'msg_adzan')) {
                $table->text('msg_adzan')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'msg_iqamah')) {
                $table->text('msg_iqamah')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'msg_shalat')) {
                $table->text('msg_shalat')->nullable();
            }
            if (!Schema::hasColumn('app_settings', 'prayer_mode_message')) {
                $table->text('prayer_mode_message')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // No action needed
        });
    }
};
