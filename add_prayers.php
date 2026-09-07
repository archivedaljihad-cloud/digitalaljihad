<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$imsak = \App\Models\JadwalSholat::where('nama_sholat', 'Imsak')->first();
if (!$imsak) {
    \App\Models\JadwalSholat::create([
        'nama_sholat' => 'Imsak',
        'waktu' => '03:50',
    ]);
    echo "Imsak added. ";
}

$syuruk = \App\Models\JadwalSholat::where('nama_sholat', 'Syuruk')->first();
if (!$syuruk) {
    \App\Models\JadwalSholat::create([
        'nama_sholat' => 'Syuruk',
        'waktu' => '05:30',
    ]);
    echo "Syuruk added.";
}

echo " Done.";
