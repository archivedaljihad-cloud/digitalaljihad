<?php
// app/Services/JadwalSholatAutoUpdateService.php

namespace App\Services;

use App\Models\JadwalSholat;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalSholatAutoUpdateService
{
    protected $aladhanUrl = 'https://api.aladhan.com/v1/timingsByCity';
    protected $kemenagBaseUrl = 'https://api.myquran.com/v2/sholat';
    protected $city = 'Kabupaten Bekasi';
    protected $country = 'Indonesia';
    protected $method = 20; // 20 = Kementerian Agama Republik Indonesia

    /**
     * Cari ID Kota/Kabupaten resmi Kemenag RI
     */
    public function getKemenagCityId($cityName = 'Kabupaten Bekasi')
    {
        $clean = strtolower(trim($cityName));

        // Prioritas pemetaan langsung untuk efisiensi
        if (str_contains($clean, 'bekasi')) {
            if (str_contains($clean, 'kota')) {
                return '1221'; // KOTA BEKASI
            }
            return '1203'; // KAB. BEKASI (Default Masjid Jami' Al Jihad)
        }

        if (str_contains($clean, 'jakarta')) {
            return '1301'; // KOTA JAKARTA
        }

        // Cari dinamis via API MyQuran Kemenag
        try {
            $response = Http::withoutVerifying()->timeout(5)->get($this->kemenagBaseUrl . '/kota/cari/' . urlencode($cityName));
            if ($response->successful()) {
                $data = $response->json('data');
                if (!empty($data) && isset($data[0]['id'])) {
                    return $data[0]['id'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mencari kode kota Kemenag: ' . $e->getMessage());
        }

        return '1203'; // Default fallback: KAB. BEKASI
    }

    /**
     * Ambil jadwal sholat langsung dari database Bimas Islam Kemenag RI
     */
    public function fetchFromKemenag($cityName = 'Kabupaten Bekasi', $date = null)
    {
        try {
            $dateObj = $date ? Carbon::parse($date) : Carbon::now('Asia/Jakarta');
            $cityId = $this->getKemenagCityId($cityName);
            $url = $this->kemenagBaseUrl . "/jadwal/{$cityId}/{$dateObj->format('Y/m/d')}";

            Log::info("Mengambil jadwal sholat resmi Kemenag RI untuk {$cityName} (ID: {$cityId}): {$url}");

            $response = Http::withoutVerifying()->timeout(8)->get($url);

            if ($response->successful()) {
                $jadwal = $response->json('data.jadwal');
                if ($jadwal) {
                    return [
                        'source'  => 'Bimas Islam Kemenag RI',
                        'lokasi'  => $response->json('data.lokasi') ?? $cityName,
                        'tanggal' => $jadwal['tanggal'] ?? $dateObj->translatedFormat('l, d/m/Y'),
                        'timings' => [
                            'Imsak'   => Carbon::parse($jadwal['imsak'])->format('H:i:s'),
                            'Subuh'   => Carbon::parse($jadwal['subuh'])->format('H:i:s'),
                            'Syuruk'  => Carbon::parse($jadwal['terbit'])->format('H:i:s'),
                            'Dzuhur'  => Carbon::parse($jadwal['dzuhur'])->format('H:i:s'),
                            'Ashar'   => Carbon::parse($jadwal['ashar'])->format('H:i:s'),
                            'Maghrib' => Carbon::parse($jadwal['maghrib'])->format('H:i:s'),
                            'Isya'    => Carbon::parse($jadwal['isya'])->format('H:i:s'),
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil data dari API Kemenag MyQuran: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fallback: Ambil dari Aladhan API dengan metode Kemenag RI (Method 20)
     */
    public function fetchFromAladhan($city = 'Kabupaten Bekasi', $country = 'Indonesia', $method = 20)
    {
        try {
            $response = Http::withoutVerifying()->timeout(8)->get($this->aladhanUrl, [
                'city'    => $city,
                'country' => $country,
                'method'  => $method,
            ]);

            if ($response->successful()) {
                $timings = $response->json('data.timings');
                if ($timings) {
                    return [
                        'source'  => 'Aladhan API (Kemenag Calculation)',
                        'lokasi'  => "{$city}, {$country}",
                        'tanggal' => Carbon::now('Asia/Jakarta')->translatedFormat('l, d/m/Y'),
                        'timings' => [
                            'Imsak'   => isset($timings['Imsak']) ? Carbon::parse($timings['Imsak'])->format('H:i:s') : null,
                            'Subuh'   => isset($timings['Fajr']) ? Carbon::parse($timings['Fajr'])->format('H:i:s') : null,
                            'Syuruk'  => isset($timings['Sunrise']) ? Carbon::parse($timings['Sunrise'])->format('H:i:s') : null,
                            'Dzuhur'  => isset($timings['Dhuhr']) ? Carbon::parse($timings['Dhuhr'])->format('H:i:s') : null,
                            'Ashar'   => isset($timings['Asr']) ? Carbon::parse($timings['Asr'])->format('H:i:s') : null,
                            'Maghrib' => isset($timings['Maghrib']) ? Carbon::parse($timings['Maghrib'])->format('H:i:s') : null,
                            'Isya'    => isset($timings['Isha']) ? Carbon::parse($timings['Isha'])->format('H:i:s') : null,
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data dari Aladhan API: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Terapkan hasil waktu sholat ke tabel database `jadwal_sholat`
     */
    protected function applyTimings(array $timings, &$results = [])
    {
        $updated = false;

        foreach ($timings as $sholatName => $waktu) {
            if (!$waktu) continue;

            // Dukungan penamaan Syuruk vs Terbit
            $targetName = $sholatName;
            if ($sholatName === 'Syuruk') {
                if (JadwalSholat::where('nama_sholat', 'Terbit')->exists() && !JadwalSholat::where('nama_sholat', 'Syuruk')->exists()) {
                    $targetName = 'Terbit';
                }
            }

            $jadwal = JadwalSholat::where('nama_sholat', $targetName)->first();

            if ($jadwal) {
                $oldTime = $jadwal->waktu;
                if ($jadwal->waktu != $waktu) {
                    $jadwal->update(['waktu' => $waktu]);
                    $updated = true;
                    $results[] = "{$targetName}: {$oldTime} -> {$waktu}";
                } else {
                    $results[] = "{$targetName}: {$waktu} (tetap)";
                }
            } else {
                JadwalSholat::create([
                    'nama_sholat' => $targetName,
                    'waktu'       => $waktu,
                ]);
                $updated = true;
                $results[] = "{$targetName}: {$waktu} (baru ditambahkan)";
            }
        }

        return $updated;
    }

    /**
     * Update jadwal sholat otomatis harian
     */
    public function updateFromAPI()
    {
        try {
            $setting = AppSetting::first();

            if (!$setting || !$setting->auto_update_jadwal) {
                Log::info('Auto-update dinonaktifkan atau setting tidak ditemukan');
                return false;
            }

            $city    = $setting->auto_update_city ?? 'Kabupaten Bekasi';
            $country = $setting->auto_update_country ?? 'Indonesia';
            $method  = $setting->auto_update_method ?? 20;

            $result = $this->forceUpdate($city, $country, $method);

            return $result['success'] ?? false;
        } catch (\Throwable $e) {
            Log::error('Error auto-update jadwal sholat: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Paksa perbarui jadwal sholat sekarang (Kemenag RI Utama + Fallback)
     */
    public function forceUpdate($city = null, $country = null, $method = null)
    {
        try {
            $setting = AppSetting::first();

            $city    = $city ?? ($setting->auto_update_city ?? 'Kabupaten Bekasi');
            $country = $country ?? ($setting->auto_update_country ?? 'Indonesia');
            $method  = $method ?? ($setting->auto_update_method ?? 20);

            $results = [];
            $data = null;

            // 1. Coba ambil dari Database Resmi Bimas Islam Kemenag RI terlebih dahulu
            $data = $this->fetchFromKemenag($city);

            // 2. Jika gagal, gunakan fallback Aladhan API (Metode 20 Kemenag RI)
            if (!$data) {
                Log::info('Menggunakan fallback Aladhan API');
                $data = $this->fetchFromAladhan($city, $country, $method);
            }

            if (!$data || empty($data['timings'])) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengambil data jadwal sholat dari server Kemenag maupun server cadangan.'
                ];
            }

            // Simpan perubahan ke tabel database
            $updated = $this->applyTimings($data['timings'], $results);

            // Update waktu sinkronisasi terakhir di pengaturan
            if ($setting) {
                $setting->auto_update_city = $city;
                $setting->last_auto_update = now();
                $setting->save();
            }

            $lokasiTersinkron = $data['lokasi'] ?? $city;
            $sumberData       = $data['source'] ?? 'Kemenag RI';

            return [
                'success'     => true,
                'message'     => "Update berhasil disinkronkan dengan {$sumberData} untuk {$lokasiTersinkron}",
                'city'        => $city,
                'country'     => $country,
                'source'      => $sumberData,
                'results'     => $results,
                'updated'     => $updated,
                'last_update' => now()->format('Y-m-d H:i:s')
            ];
        } catch (\Throwable $e) {
            Log::error('Error force update: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hitung manual berdasarkan koordinat (jika dibutuhkan)
     */
    public function calculateManual($latitude, $longitude, $timezone = 'Asia/Jakarta')
    {
        try {
            $today = Carbon::now($timezone);
            $date  = $today->format('d-m-Y');

            $response = Http::withoutVerifying()->timeout(8)->get('https://api.aladhan.com/v1/timings/' . $date, [
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'method'    => $this->method,
            ]);

            if (!$response->successful()) {
                return false;
            }

            $data = $response->json('data.timings');
            if (!$data) return false;

            $timings = [
                'Imsak'   => isset($data['Imsak']) ? Carbon::parse($data['Imsak'])->format('H:i:s') : null,
                'Subuh'   => isset($data['Fajr']) ? Carbon::parse($data['Fajr'])->format('H:i:s') : null,
                'Syuruk'  => isset($data['Sunrise']) ? Carbon::parse($data['Sunrise'])->format('H:i:s') : null,
                'Dzuhur'  => isset($data['Dhuhr']) ? Carbon::parse($data['Dhuhr'])->format('H:i:s') : null,
                'Ashar'   => isset($data['Asr']) ? Carbon::parse($data['Asr'])->format('H:i:s') : null,
                'Maghrib' => isset($data['Maghrib']) ? Carbon::parse($data['Maghrib'])->format('H:i:s') : null,
                'Isya'    => isset($data['Isha']) ? Carbon::parse($data['Isha'])->format('H:i:s') : null,
            ];

            return $this->applyTimings($timings);
        } catch (\Throwable $e) {
            Log::error('Error calculate manual jadwal sholat: ' . $e->getMessage());
            return false;
        }
    }

    public function setLocation($city, $country = 'Indonesia')
    {
        $this->city    = $city;
        $this->country = $country;

        $setting = AppSetting::first();
        if ($setting) {
            $setting->update([
                'auto_update_city'    => $city,
                'auto_update_country' => $country,
            ]);
        }
    }

    public function setMethod($method)
    {
        $this->method = $method;

        $setting = AppSetting::first();
        if ($setting) {
            $setting->update(['auto_update_method' => $method]);
        }
    }

    public function getCurrentLocation()
    {
        $setting = AppSetting::first();
        return [
            'city'    => $setting->auto_update_city ?? 'Kabupaten Bekasi',
            'country' => $setting->auto_update_country ?? 'Indonesia',
            'method'  => $setting->auto_update_method ?? 20
        ];
    }
}