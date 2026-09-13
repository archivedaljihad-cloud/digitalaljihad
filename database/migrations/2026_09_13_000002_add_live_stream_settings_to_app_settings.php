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
            if (!Schema::hasColumn('app_settings', 'live_makkah_url')) {
                $table->string('live_makkah_url')->nullable()->after('rotation_pages');
            }
            if (!Schema::hasColumn('app_settings', 'live_madinah_url')) {
                $table->string('live_madinah_url')->nullable()->after('live_makkah_url');
            }
            if (!Schema::hasColumn('app_settings', 'live_stream_audio')) {
                $table->boolean('live_stream_audio')->default(false)->after('live_madinah_url');
            }
            if (!Schema::hasColumn('app_settings', 'live_stream_overlay')) {
                $table->boolean('live_stream_overlay')->default(true)->after('live_stream_audio');
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
            if (Schema::hasColumn('app_settings', 'live_makkah_url')) $columns[] = 'live_makkah_url';
            if (Schema::hasColumn('app_settings', 'live_madinah_url')) $columns[] = 'live_madinah_url';
            if (Schema::hasColumn('app_settings', 'live_stream_audio')) $columns[] = 'live_stream_audio';
            if (Schema::hasColumn('app_settings', 'live_stream_overlay')) $columns[] = 'live_stream_overlay';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
