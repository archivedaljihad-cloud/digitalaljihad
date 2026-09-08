<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            URL::forceScheme('https');
        }

        // Auto-assign incremental ID if database table does not have AUTO_INCREMENT (e.g. TiDB)
        Event::listen('eloquent.creating: *', function ($event, array $payload) {
            $model = $payload[0] ?? null;
            if ($model instanceof Model) {
                if ($model->getKeyType() === 'int' && $model->getIncrementing() && empty($model->getKey())) {
                    try {
                        $keyName = $model->getKeyName();
                        $maxId = $model->newQuery()->max($keyName) ?? 0;
                        $model->setAttribute($keyName, (int)$maxId + 1);
                    } catch (\Throwable $e) {
                        // ignore and proceed
                    }
                }
            }
        });

        // Auto-heal missing slide image files in database if lost
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('slides')) {
                $slides = \App\Models\Slide::all();
                foreach ($slides as $s) {
                    $storageFile = storage_path('app/public/' . $s->gambar);
                    $publicFile = public_path('storage/' . $s->gambar);
                    if (!file_exists($storageFile) && !file_exists($publicFile)) {
                        $text = strtolower(($s->judul ?? '') . ' ' . ($s->deskripsi ?? ''));
                        if (str_contains($text, 'kemenag') || str_contains($text, 'simas')) {
                            $s->update(['gambar' => 'slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp']);
                        } elseif (str_contains($text, 'sertifikat') || str_contains($text, 'berkiblat') || str_contains($text, '1.148') || str_contains($text, '1.448')) {
                            $s->update(['gambar' => 'slides/GkxyYVJO2IdZoU1X6mgSNUcghs0gu1HqVtYlxgYA.png']);
                        } elseif (str_contains($text, 'qiblat') || str_contains($text, 'arah')) {
                            $s->update(['gambar' => 'slides/1gdpqFYCyv7Sv0qLDTpyxSjMnknbVEM9OLVOjPM3.png']);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore and proceed
        }

        // Auto-provision table keuangan_ambulance if not exists
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('keuangan_ambulance')) {
                \Illuminate\Support\Facades\Schema::create('keuangan_ambulance', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->date('tanggal');
                    $table->string('deskripsi');
                    $table->decimal('pemasukan', 15, 2)->default(0.00);
                    $table->decimal('pengeluaran', 15, 2)->default(0.00);
                    $table->decimal('saldo', 15, 2)->default(0.00);
                    $table->string('kategori', 100)->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // ignore and proceed
        }

        // Auto-provision table program_infaq & donasi_infaq if not exists
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('program_infaq')) {
                \Illuminate\Support\Facades\Schema::create('program_infaq', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('nama_program');
                    $table->text('keterangan')->nullable();
                    $table->decimal('target_dana', 15, 2)->default(0.00);
                    $table->date('tanggal_mulai')->nullable();
                    $table->date('tanggal_selesai')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('donasi_infaq')) {
                \Illuminate\Support\Facades\Schema::create('donasi_infaq', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('program_infaq_id');
                    $table->string('nama_donatur')->default('Hamba Allah');
                    $table->boolean('is_anonim')->default(false);
                    $table->decimal('nominal', 15, 2)->default(0.00);
                    $table->date('tanggal');
                    $table->string('keterangan')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // ignore and proceed
        }

        // Auto-provision missing columns in table app_settings if not exists (e.g. TiDB Cloud)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                \Illuminate\Support\Facades\Schema::table('app_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'tarhim_trigger_seconds')) {
                        $table->integer('tarhim_trigger_seconds')->nullable()->default(300);
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'tarhim_audio')) {
                        $table->string('tarhim_audio')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'tarhim_audio_subuh')) {
                        $table->string('tarhim_audio_subuh')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'tarhim_audio_reguler')) {
                        $table->string('tarhim_audio_reguler')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'prayer_bg_image')) {
                        $table->string('prayer_bg_image')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'prayer_bg_opacity')) {
                        $table->integer('prayer_bg_opacity')->nullable()->default(80);
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'msg_countdown')) {
                        $table->text('msg_countdown')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'msg_adzan')) {
                        $table->text('msg_adzan')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'msg_iqamah')) {
                        $table->text('msg_iqamah')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'msg_shalat')) {
                        $table->text('msg_shalat')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'prayer_mode_message')) {
                        $table->text('prayer_mode_message')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore and proceed
        }

        // Auto-provision missing columns in table pengumuman if not exists (e.g. TiDB Cloud)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('pengumuman')) {
                \Illuminate\Support\Facades\Schema::table('pengumuman', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('pengumuman', 'judul')) {
                        $table->string('judul')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('pengumuman', 'pemateri')) {
                        $table->string('pemateri')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('pengumuman', 'foto')) {
                        $table->string('foto')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('pengumuman', 'waktu')) {
                        $table->string('waktu')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('pengumuman', 'tempat')) {
                        $table->string('tempat')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore and proceed
        }

        // Membuat variabel $setting otomatis ada di layouts.admin
        View::composer('layouts.admin', function ($view) {
            $view->with('setting', AppSetting::first());
        });
    }
}