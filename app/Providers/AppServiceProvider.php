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

        // Membuat variabel $setting otomatis ada di layouts.admin
        View::composer('layouts.admin', function ($view) {
            $view->with('setting', AppSetting::first());
        });
    }
}