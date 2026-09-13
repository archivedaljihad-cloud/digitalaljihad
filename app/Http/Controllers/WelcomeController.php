<?php
namespace App\Http\Controllers;
use App\Models\AppSetting;
use App\Models\JadwalSholat;
use App\Models\SholatJumat;
use App\Models\Pengumuman;
use App\Models\Keuangan;
use App\Models\Qris;
use App\Models\Slide;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
class WelcomeController extends Controller
{
    public function rotator()
    {
        $settings = AppSetting::first();
        if (!$settings) {
            $settings = AppSetting::create([
                'nama_aplikasi'       => 'MASJID AL-IKHLAS',
                'footer'              => 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System',
                'running_text'        => '🌙 "Hati yang tenang ada pada mereka yang selalu mengingat Allah. Mari perbanyak zikir dan shalat berjamaah."',
                'auto_update_jadwal' => true,
                'auto_update_city'   => 'Jakarta',
                'auto_update_country'=> 'Indonesia',
                'rotation_interval'  => 10,
                'rotation_enabled'   => true,
                'rotation_pages' => json_encode([
                    [
                        'url'    => 'welcome-embed',
                        'name'   => 'Dashboard Lengkap',
                        'active' => false
                    ],
                    [
                        'url'    => 'utama-embed',
                        'name'   => 'Jadwal Sholat',
                        'active' => true
                    ],
                    [
                        'url'    => 'keuangan-embed',
                        'name'   => 'Rincian Keuangan',
                        'active' => true
                    ],
                    [
                        'url'    => 'jumat-embed',
                        'name'   => 'Jadwal Sholat Jumat',
                        'active' => true
                    ],
                    [
                        'url'    => 'pengumuman-embed',
                        'name'   => 'Pengumuman',
                        'active' => true
                    ],
                    [
                        'url'    => 'keuangan-summary-embed',
                        'name'   => 'Ringkasan Keuangan',
                        'active' => true
                    ],
                    [
                        'url'    => 'qris-embed',
                        'name'   => 'QRIS Donasi',
                        'active' => true
                    ],
                    [
                        'url'    => 'slide-embed',
                        'name'   => 'Slide Informasi',
                        'active' => true
                    ],
                    [
                        'url'    => 'idul-fitri-embed',
                        'name'   => 'Idul Fitri',
                        'active' => true
                    ],
                    [
                        'url'    => 'idul-adha-embed',
                        'name'   => 'Idul Adha',
                        'active' => true
                    ],
                    [
                        'url'    => 'ambulance-embed',
                        'name'   => 'Rincian Kas Ambulance',
                        'active' => true
                    ],
                    [
                        'url'    => 'infaq-embed',
                        'name'   => 'Penggalangan Infaq',
                        'active' => true
                    ]
                ])
            ]);
        }
        $this->syncJadwalSholatHariIni($settings);
        return view('rotator', [
            'rotationInterval' => $settings->getRotationInterval(),
            'rotationEnabled'  => $settings->isRotationEnabled(),
            'rotationPages'    => $settings->getRotationPagesList()
        ]);
    }
    public function welcomeEmbed()
    {
        $settings = AppSetting::first();
        if (!$settings) {
            $settings = AppSetting::create([
                'nama_aplikasi'       => 'MASJID AL-IKHLAS',
                'footer'              => '',
                'running_text'        => '',
                'auto_update_jadwal'  => true,
                'auto_update_city'    => 'Kabupaten Bekasi',
                'auto_update_country' => 'Indonesia',
                'auto_update_method'  => 20,
            ]);
        }
        $this->syncJadwalSholatHariIni($settings);
        $jadwalSholat = JadwalSholat::urutkan()->get();
        $today = Carbon::today('Asia/Jakarta');
        $sholatJumat = SholatJumat::where('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->first();
        $pengumuman = Pengumuman::where('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->take(3)
            ->get();
        $keuanganSummary = [
            'total_pemasukan'  => Keuangan::sum('pemasukan'),
            'total_pengeluaran'=> Keuangan::sum('pengeluaran'),
            'saldo'            => Keuangan::sum('pemasukan') - Keuangan::sum('pengeluaran'),
        ];
        $qris = Qris::aktif()->first();
        $slides = Slide::aktif()
            ->urut()
            ->get();
        return view(
            'welcome',
            compact(
                'settings',
                'jadwalSholat',
                'sholatJumat',
                'pengumuman',
                'keuanganSummary',
                'qris',
                'slides'
            )
        );
    }                          
    public function utamaEmbed()
    {
        $settings = AppSetting::first();
        $this->syncJadwalSholatHariIni($settings);
        $jadwalSholat = JadwalSholat::urutkan()->get();
        return view(
            'utama',
            compact(
                'settings',
                'jadwalSholat'
            )
        );
    }

    private function syncJadwalSholatHariIni($settings)
    {
        if ($settings && $settings->auto_update_jadwal) {
            $today = Carbon::today('Asia/Jakarta');
            $lastUpdate = $settings->last_auto_update ? Carbon::parse($settings->last_auto_update)->timezone('Asia/Jakarta')->startOfDay() : null;
            
            if (!$lastUpdate || $lastUpdate->lt($today)) {
                try {
                    app(\App\Services\JadwalSholatAutoUpdateService::class)->updateFromAPI();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Auto-sync Kemenag on TV load: ' . $e->getMessage());
                }
            }
        }
    }
    public function keuanganEmbed()
    {
        $settings = AppSetting::first();
        $keuangan = Keuangan::orderBy('tanggal', 'desc')->get();
        $totalPemasukan = Keuangan::sum('pemasukan');
        $totalPengeluaran = Keuangan::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;
        return view(
            'keuangan',
            compact(
                'settings',
                'keuangan',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo'
            )
        );
    }
    public function jumatEmbed()
    {
        $settings = AppSetting::first();
        $jadwalSholat = JadwalSholat::urutkan()->get();
        $today = Carbon::today('Asia/Jakarta');
        $sholatJumat = SholatJumat::where('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->first();
        return view(
            'jumat',
            compact(
                'settings',
                'jadwalSholat',
                'sholatJumat'
            )
        );
    }
    public function pengumumanEmbed()
    {
        $settings = AppSetting::first();
        $today = Carbon::today('Asia/Jakarta');
        $pengumuman = Pengumuman::where('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->get();
        return view(
            'pengumuman',
            compact(
                'settings',
                'pengumuman'
            )
        );
    }
    public function keuanganSummaryEmbed()
    {
        $settings = AppSetting::first();
        $totalPemasukan = Keuangan::sum('pemasukan');
        $totalPengeluaran = Keuangan::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;
        $recentTransactions = Keuangan::orderBy('tanggal', 'desc')
            ->take(10)
            ->get();
        return view(
            'keuangan-summary',
            compact(
                'settings',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo',
                'recentTransactions'
            )
        );
    }
    public function slideEmbed()
    {
        $settings = AppSetting::first();
        $slides = Slide::aktif()
            ->urut()
            ->get();
        return view(
            'slide-embed',
            compact('slides', 'settings')
        );
    }                        
    public function getRotationSettings()
    {
        $settings = AppSetting::first();
        return response()->json([
            'interval' => $settings
                ? (int) $settings->getRotationInterval()
                : 10,
            'enabled' => $settings
                ? $settings->isRotationEnabled()
                : true,
            'pages' => $settings
                ? $settings->getRotationPagesList()
                : [],
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    public function getDataTimestamp()
    {
        $timestamp = Cache::remember(
            'data_timestamp',
            60,
            function () {
                $latestTimestamps = [
                    'app_settings'   => AppSetting::max('updated_at'),
                    'jadwal_sholat'  => JadwalSholat::max('updated_at'),
                    'sholat_jumat'   => SholatJumat::max('updated_at'),
                    'pengumuman'     => Pengumuman::max('updated_at'),
                    'keuangan'       => Keuangan::max('updated_at'),
                    'slides'         => Slide::max('updated_at'),
                ];
                $latestTimestamp = collect($latestTimestamps)
                    ->filter()
                    ->max();
                $updatedData = [];
                foreach ($latestTimestamps as $key => $value) {
                    if ($value && $value == $latestTimestamp) {
                        $updatedData[] = $key;
                    }
                }
                return [
                    'timestamp' => $latestTimestamp
                        ? Carbon::parse($latestTimestamp)->toIso8601String()
                        : null,
                    'updated_data' => $updatedData,
                ];
            }
        );
        $settings = AppSetting::first();
        return response()->json(
            array_merge(
                $timestamp,
                [
                    'check_time' => now()->toIso8601String(),
                    'auto_update_status' => $settings->auto_update_jadwal ?? false,
                    'auto_update_location' => $settings->auto_update_city ?? 'Jakarta',
                ]
            )
        );
    }
}
