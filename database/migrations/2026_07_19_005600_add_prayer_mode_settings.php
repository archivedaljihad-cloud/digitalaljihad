<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kita buat tabel 'app_settings' terlebih dahulu jika belum ada
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // 2. Setelah tabelnya dipastikan ada, baru kita masukkan datanya
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
            DB::table('app_settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }

    public function down(): void
    {
        // Menghapus tabel jika dilakukan rollback
        Schema::dropIfExists('app_settings');
    }
};