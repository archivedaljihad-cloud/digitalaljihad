<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = \App\Models\AppSetting::first();
if ($setting) {
    // Current footer: Copyright &copy; <a href="...">DKM AL JIHAD Dev.Syatem</a> 2026
    // New footer: Copyright &copy; DKM AL JIHAD Dev.System 2026
    $setting->footer = 'Copyright &copy; 2026 DKM AL JIHAD Dev.System';
    $setting->save();
    echo "Updated to: " . $setting->footer;
} else {
    echo "No settings found.";
}
