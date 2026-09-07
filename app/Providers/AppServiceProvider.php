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

        // Membuat variabel $setting otomatis ada di layouts.admin
        View::composer('layouts.admin', function ($view) {
            $view->with('setting', AppSetting::first());
        });
    }
}