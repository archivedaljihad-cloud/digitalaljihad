<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        Model::creating(function ($model) {
            if ($model->getKeyType() === 'int' && $model->getIncrementing() && empty($model->getKey())) {
                $maxId = $model->newQuery()->max($model->getKeyName()) ?? 0;
                $model->setAttribute($model->getKeyName(), $maxId + 1);
            }
        });

        // Membuat variabel $setting otomatis ada di layouts.admin
        View::composer('layouts.admin', function ($view) {
            $view->with('setting', AppSetting::first());
        });
    }
}