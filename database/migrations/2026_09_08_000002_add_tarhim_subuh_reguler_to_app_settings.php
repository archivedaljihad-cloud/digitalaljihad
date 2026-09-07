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
            if (!Schema::hasColumn('app_settings', 'tarhim_audio_subuh')) {
                $table->string('tarhim_audio_subuh')->nullable()->after('tarhim_audio');
            }
            if (!Schema::hasColumn('app_settings', 'tarhim_audio_reguler')) {
                $table->string('tarhim_audio_reguler')->nullable()->after('tarhim_audio_subuh');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if (Schema::hasColumn('app_settings', 'tarhim_audio_subuh')) {
                $table->dropColumn('tarhim_audio_subuh');
            }
            if (Schema::hasColumn('app_settings', 'tarhim_audio_reguler')) {
                $table->dropColumn('tarhim_audio_reguler');
            }
        });
    }
};
