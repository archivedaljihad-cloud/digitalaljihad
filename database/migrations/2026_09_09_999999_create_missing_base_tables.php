<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sholat Jumat
        if (!Schema::hasTable('sholat_jumat')) {
            Schema::create('sholat_jumat', function (Blueprint $table) {
                $table->id();
                $table->string('imam')->nullable();
                $table->string('khatib')->nullable();
                $table->string('muadzin')->nullable();
                $table->date('tanggal');
                $table->timestamps();
            });
        }

        // 2. Sholat Idul Fitri
        if (!Schema::hasTable('sholat_idul_fitri')) {
            Schema::create('sholat_idul_fitri', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun');
                $table->date('tanggal');
                $table->string('imam')->nullable();
                $table->string('khatib')->nullable();
                $table->string('muadzin')->nullable();
                $table->time('waktu')->default('07:00:00');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Sholat Idul Adha
        if (!Schema::hasTable('sholat_idul_adha')) {
            Schema::create('sholat_idul_adha', function (Blueprint $table) {
                $table->id();
                $table->integer('tahun');
                $table->date('tanggal');
                $table->string('imam')->nullable();
                $table->string('khatib')->nullable();
                $table->string('muadzin')->nullable();
                $table->time('waktu')->default('07:00:00');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 4. Jadwal Sholat
        if (!Schema::hasTable('jadwal_sholat')) {
            Schema::create('jadwal_sholat', function (Blueprint $table) {
                $table->id();
                $table->string('nama_sholat');
                $table->time('waktu');
                $table->integer('durasi_adzan')->nullable()->default(5);
                $table->integer('durasi_iqamah')->nullable()->default(10);
                $table->integer('durasi_sholat')->nullable()->default(10);
                $table->timestamps();
            });

            $defaults = [
                ['nama_sholat' => 'Subuh', 'waktu' => '04:38:00'],
                ['nama_sholat' => 'Dzuhur', 'waktu' => '11:55:00'],
                ['nama_sholat' => 'Ashar', 'waktu' => '15:16:00'],
                ['nama_sholat' => 'Maghrib', 'waktu' => '17:54:00'],
                ['nama_sholat' => 'Isya', 'waktu' => '19:04:00'],
            ];
            foreach ($defaults as $d) {
                DB::table('jadwal_sholat')->insert(array_merge($d, [
                    'durasi_adzan' => 5,
                    'durasi_iqamah' => 10,
                    'durasi_sholat' => 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 5. Keuangan
        if (!Schema::hasTable('keuangan')) {
            Schema::create('keuangan', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('deskripsi');
                $table->decimal('pemasukan', 15, 2)->default(0.00);
                $table->decimal('pengeluaran', 15, 2)->default(0.00);
                $table->decimal('saldo', 15, 2)->default(0.00);
                $table->string('kategori')->nullable();
                $table->timestamps();
            });
        }

        // 6. Pengumuman
        if (!Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->id();
                $table->text('isi');
                $table->date('tanggal')->nullable();
                $table->string('judul')->nullable();
                $table->string('kategori')->nullable();
                $table->string('prioritas')->default('normal');
                $table->string('gambar')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        // 7. QRIS
        if (!Schema::hasTable('qris')) {
            Schema::create('qris', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('gambar');
                $table->text('keterangan')->nullable();
                $table->string('nomor_rekening', 100)->nullable();
                $table->string('bank', 100)->nullable();
                $table->string('atas_nama')->nullable();
                $table->string('status', 20)->default('aktif');
                $table->timestamps();
            });
        }

        // 8. Base columns for app_settings
        if (Schema::hasTable('app_settings')) {
            Schema::table('app_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('app_settings', 'nama_aplikasi')) {
                    $table->string('nama_aplikasi')->default('DISPLAY MASJID');
                }
                if (!Schema::hasColumn('app_settings', 'favicon')) {
                    $table->string('favicon')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'background')) {
                    $table->string('background')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'logo')) {
                    $table->string('logo')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'footer')) {
                    $table->text('footer')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'running_text')) {
                    $table->text('running_text')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_jadwal')) {
                    $table->boolean('auto_update_jadwal')->default(false);
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_frequency')) {
                    $table->string('auto_update_frequency')->default('daily');
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_time')) {
                    $table->time('auto_update_time')->default('00:00:00');
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_city')) {
                    $table->string('auto_update_city')->default('Jakarta');
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_country')) {
                    $table->string('auto_update_country')->default('Indonesia');
                }
                if (!Schema::hasColumn('app_settings', 'auto_update_method')) {
                    $table->integer('auto_update_method')->default(11);
                }
                if (!Schema::hasColumn('app_settings', 'rotation_interval')) {
                    $table->integer('rotation_interval')->default(10);
                }
                if (!Schema::hasColumn('app_settings', 'rotation_enabled')) {
                    $table->boolean('rotation_enabled')->default(true);
                }
                if (!Schema::hasColumn('app_settings', 'rotation_pages')) {
                    $table->text('rotation_pages')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'last_auto_update')) {
                    $table->timestamp('last_auto_update')->nullable();
                }
                if (!Schema::hasColumn('app_settings', 'prayer_mode_before_adzan')) {
                    $table->integer('prayer_mode_before_adzan')->default(5);
                }
                if (!Schema::hasColumn('app_settings', 'prayer_mode_adzan_duration')) {
                    $table->integer('prayer_mode_adzan_duration')->default(5);
                }
                if (!Schema::hasColumn('app_settings', 'prayer_mode_iqamah_duration')) {
                    $table->integer('prayer_mode_iqamah_duration')->default(10);
                }
                if (!Schema::hasColumn('app_settings', 'prayer_mode_after_prayer')) {
                    $table->integer('prayer_mode_after_prayer')->default(10);
                }
                if (!Schema::hasColumn('app_settings', 'prayer_mode_theme')) {
                    $table->string('prayer_mode_theme', 50)->default('green');
                }
                if (!Schema::hasColumn('app_settings', 'audio_tarhim')) {
                    $table->string('audio_tarhim')->nullable();
                }
            });

            // Insert default row in app_settings if empty
            if (DB::table('app_settings')->count() === 0) {
                DB::table('app_settings')->insert([
                    'nama_aplikasi' => 'DISPLAY MASJID',
                    'footer' => 'Copyright &copy; DKM AL JIHAD Dev.System 2026',
                    'running_text' => '🌙 "Hati yang tenang ada pada mereka yang selalu mengingat Allah. Mari perbanyak zikir dan shalat berjamaah." — (QS. Ar-Ra\'d: 28)',
                    'auto_update_jadwal' => true,
                    'auto_update_frequency' => 'daily',
                    'auto_update_time' => '00:00:00',
                    'auto_update_city' => 'Jakarta',
                    'auto_update_country' => 'Indonesia',
                    'auto_update_method' => 11,
                    'rotation_interval' => 10,
                    'rotation_enabled' => true,
                    'rotation_pages' => json_encode([
                        ['url' => 'welcome-embed', 'name' => 'Dashboard Lengkap', 'active' => false],
                        ['url' => 'utama-embed', 'name' => 'Jadwal Sholat', 'active' => true],
                        ['url' => 'keuangan-embed', 'name' => 'Rincian Keuangan', 'active' => false],
                        ['url' => 'jumat-embed', 'name' => 'Jadwal Sholat Jumat', 'active' => false],
                        ['url' => 'pengumuman-embed', 'name' => 'Pengumuman', 'active' => false],
                        ['url' => 'keuangan-summary-embed', 'name' => 'Ringkasan Keuangan', 'active' => true],
                        ['url' => 'qris-embed', 'name' => 'QRIS Donasi', 'active' => false],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive rollback
    }
};
