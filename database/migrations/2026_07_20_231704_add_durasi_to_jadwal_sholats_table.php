<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        // Cek terlebih dahulu apakah tabel 'settings' memang ada di database
        if (Schema::hasTable('settings')) {
            $settings = [
                [
                    'key' => 'prayer_mode_enabled',
                    'value' => '1',
                ],
                [
                    'key' => 'prayer_mode_duration',
                    'value' => '10',
                ],
                [
                    'key' => 'prayer_mode_message',
                    'value' => 'Silakan melaksanakan shalat dengan khusyuk.',
                ],
            ];

            foreach ($settings as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    ['value' => $setting['value']]
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->whereIn('key', [
                    'prayer_mode_enabled',
                    'prayer_mode_duration',
                    'prayer_mode_message'
                ])
                ->delete();
        }
    }
};