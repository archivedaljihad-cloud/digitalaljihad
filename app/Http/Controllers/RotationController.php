<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RotationController extends Controller
{
    /**
     * Master katalog metadata seluruh halaman embed display masjid
     */
    private function getMasterPageCatalog(): array
    {
        return [
            'welcome-embed' => [
                'name' => 'Dashboard Lengkap',
                'desc' => 'Tampilan lengkap dengan jadwal sholat, jumat, pengumuman, dan keuangan',
            ],
            'utama-embed' => [
                'name' => 'Jadwal Sholat',
                'desc' => 'Tampilan fokus jadwal sholat 5 waktu',
            ],
            'keuangan-embed' => [
                'name' => 'Rincian Keuangan',
                'desc' => 'Tampilan detail laporan keuangan lengkap',
            ],
            'jumat-embed' => [
                'name' => 'Jadwal Sholat Jumat',
                'desc' => 'Informasi imam, khatib, dan muadzin sholat jumat',
            ],
            'pengumuman-embed' => [
                'name' => 'Pengumuman',
                'desc' => 'Daftar pengumuman terbaru untuk jamaah',
            ],
            'keuangan-summary-embed' => [
                'name' => 'Ringkasan Keuangan',
                'desc' => 'Ringkasan keuangan dengan grafik donat',
            ],
            'qris-embed' => [
                'name' => 'QRIS Donasi',
                'desc' => 'QR Code untuk donasi dan infak online',
            ],
            'slide-embed' => [
                'name' => 'Slide Informasi',
                'desc' => 'Slideshow informasi, gambar, dan pengumuman bergambar',
            ],
            'idul-fitri-embed' => [
                'name' => 'Idul Fitri',
                'desc' => 'Jadwal sholat Idul Fitri dengan imam, khatib, muadzin',
            ],
            'idul-adha-embed' => [
                'name' => 'Idul Adha',
                'desc' => 'Jadwal sholat Idul Adha dengan imam, khatib, muadzin',
            ],
            'ambulance-embed' => [
                'name' => 'Rincian Kas Ambulance',
                'desc' => 'Tampilan rincian keuangan kas ambulance lengkap',
            ],
            'infaq-embed' => [
                'name' => 'Penggalangan Infaq',
                'desc' => 'Program infaq dan donasi pembangunan masjid',
            ],
            'live-mekah-embed' => [
                'name' => 'Live TV Mekah (Masjidil Haram)',
                'desc' => 'Siaran langsung 24 jam Masjidil Haram Ka\'bah Makkah Al-Mukarramah',
            ],
            'live-madinah-embed' => [
                'name' => 'Live TV Madinah (Masjid Nabawi)',
                'desc' => 'Siaran langsung 24 jam Masjid Nabawi Madinah Al-Munawwarah',
            ],
            'hikmah-embed' => [
                'name' => 'Mutiara Hadits & Hikmah',
                'desc' => 'Hadits shahih tematik & tadabbur mutiara hikmah harian dengan AI',
            ],
        ];
    }

    /**
     * Menampilkan halaman konfigurasi rotasi
     */
    public function index()
    {
        $setting = AppSetting::first();
        if (!$setting) {
            $setting = AppSetting::create([
                'nama_aplikasi'       => 'MASJID AL-IKHLAS',
                'footer'              => 'Copyright 2026',
                'running_text'        => 'Selamat datang di Sistem Informasi Masjid',
                'auto_update_jadwal'  => true,
                'auto_update_city'    => 'Jakarta',
                'auto_update_country' => 'Indonesia',
                'rotation_interval'   => 10,
                'rotation_enabled'    => true,
            ]);
        }

        $catalog = $this->getMasterPageCatalog();
        $savedPages = $setting->getRotationPages();

        // Susun daftar halaman terurut sesuai yang tersimpan di database
        $orderedPages = [];
        $handledUrls = [];

        if (is_array($savedPages)) {
            foreach ($savedPages as $sp) {
                $url = $sp['url'] ?? '';
                if ($url && isset($catalog[$url])) {
                    $orderedPages[] = [
                        'url'    => $url,
                        'name'   => $catalog[$url]['name'],
                        'desc'   => $catalog[$url]['desc'],
                        'active' => (bool) ($sp['active'] ?? false),
                    ];
                    $handledUrls[] = $url;
                }
            }
        }

        // Tambahkan halaman baru jika ada yang belum ada di daftar tersimpan
        foreach ($catalog as $url => $meta) {
            if (!in_array($url, $handledUrls)) {
                $orderedPages[] = [
                    'url'    => $url,
                    'name'   => $meta['name'],
                    'desc'   => $meta['desc'],
                    'active' => ($url !== 'welcome-embed'), // default aktif kecuali welcome-embed
                ];
            }
        }

        // Hak akses Super Admin: hanya admin/superadmin yang bisa memindahkan urutan
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('admin');

        return view('rotation.index', [
            'setting'      => $setting,
            'pages'        => $orderedPages,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Memperbarui pengaturan rotasi dan urutan tampilan
     */
    public function update(Request $request)
    {
        $setting = AppSetting::first();
        $catalog = $this->getMasterPageCatalog();
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('admin');

        $activePages = $request->input('active_pages', []);
        $newPages = [];

        if ($isSuperAdmin && $request->has('page_order') && is_array($request->input('page_order'))) {
            // SUPER ADMIN: Mengubah urutan berdasarkan input tombol naik/turun
            $submittedOrder = $request->input('page_order');
            $handledUrls = [];

            foreach ($submittedOrder as $url) {
                if (isset($catalog[$url])) {
                    $newPages[] = [
                        'url'    => $url,
                        'name'   => $catalog[$url]['name'],
                        'active' => in_array($url, $activePages),
                    ];
                    $handledUrls[] = $url;
                }
            }

            // Pastikan jika ada halaman katalog yang terlewat, ditambahkan di bagian belakang
            foreach ($catalog as $url => $meta) {
                if (!in_array($url, $handledUrls)) {
                    $newPages[] = [
                        'url'    => $url,
                        'name'   => $meta['name'],
                        'active' => in_array($url, $activePages),
                    ];
                }
            }
        } else {
            // OPERATOR BIASA: Urutan TETAP TERKUNCI (tidak bisa diubah), hanya status aktif/nonaktif yang diperbarui
            $existingPages = $setting ? $setting->getRotationPages() : [];
            $handledUrls = [];

            if (is_array($existingPages)) {
                foreach ($existingPages as $item) {
                    $url = $item['url'] ?? '';
                    if ($url && isset($catalog[$url])) {
                        $newPages[] = [
                            'url'    => $url,
                            'name'   => $catalog[$url]['name'],
                            'active' => in_array($url, $activePages),
                        ];
                        $handledUrls[] = $url;
                    }
                }
            }

            foreach ($catalog as $url => $meta) {
                if (!in_array($url, $handledUrls)) {
                    $newPages[] = [
                        'url'    => $url,
                        'name'   => $meta['name'],
                        'active' => in_array($url, $activePages),
                    ];
                }
            }
        }

        DB::table('app_settings')->update([
            'rotation_enabled'  => $request->has('rotation_enabled') ? 1 : 0,
            'rotation_interval' => max(1, min(3600, (int) $request->input('rotation_interval', 10))),
            'rotation_pages'    => json_encode($newPages),
            'updated_at'        => now(),
        ]);

        return redirect()
            ->route('rotation.index')
            ->with('success', 'Pengaturan rotasi halaman dan urutan tampilan berhasil diperbarui!');
    }
}
