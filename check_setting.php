<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s = App\Models\AppSetting::first();
echo 'before_adzan='.$s->prayer_mode_before_adzan."\n";
echo 'tarhim_trigger='.$s->tarhim_trigger_seconds."\n";
