<?php
namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\JadwalSholat;
use App\Models\SholatJumat;
use Carbon\Carbon;

class PrayerModeController extends Controller
{
    /**
     * Logika utama penentuan fase Prayer Mode.
     */
    private const PHASE_INACTIVE = 'inactive';
    private const PHASE_COUNTDOWN = 'countdown';
    private const PHASE_ADZAN = 'adzan';
    private const PHASE_IQAMAH = 'iqamah';
    private const PHASE_PRAYER = 'prayer';
    private const PHASE_KHUTBAH = 'khutbah';

    private function getPrayerState()
    {
        try {
            $setting = AppSetting::first();
        } catch (\Throwable $e) {
            $setting = null;
        }

        if (!$setting || !$setting->prayer_mode_enabled) {
            return [
                'active' => false,
                'phase' => self::PHASE_INACTIVE,
                'setting' => $setting,
            ];
        }

        $now = Carbon::now(config('app.timezone'));
        
        // Mengambil jadwal dan mengecualikan Imsak & Terbit dari Prayer Mode
        $jadwal = JadwalSholat::urutkan()->get()->filter(function ($item) {
            $nama = strtolower(trim($item->nama_sholat));
            return !in_array($nama, ['imsak', 'terbit']);
        });

        if ($jadwal->isEmpty()) {
            return [
                'active' => false,
                'phase' => self::PHASE_INACTIVE,
                'setting' => $setting,
            ];
        }

        $beforeAdzan = $setting->prayer_mode_before_adzan ?? 5;
        $adzanDuration = $setting->prayer_mode_adzan_duration ?? 4;
        $iqamahDuration = $setting->prayer_mode_iqamah_duration ?? 10;
        $prayerDuration = $setting->prayer_mode_duration ?? 10;
        $jumatDuration = $setting->getFridayPrayerDuration(); // Default 50 menit
        $theme = $setting->prayer_mode_theme ?? 'gold';

        $isFriday = $now->isFriday();

        foreach ($jadwal as $item) {
            if (empty($item->waktu)) {
                continue;
            }

            $sholatNameClean = strtolower(trim($item->nama_sholat));
            $isDzuhur = in_array($sholatNameClean, ['dzuhur', 'zuhur', 'dhuhur', "jum'at", 'jumat']);
            $isFridayPrayer = $isFriday && $isDzuhur;

            $adzanTime = Carbon::parse(
                $now->format('Y-m-d') . ' ' . $item->waktu,
                config('app.timezone')
            );

            $phase = self::PHASE_INACTIVE;
            $remaining = 0;
            $countdownStart = (clone $adzanTime)->subMinutes($beforeAdzan);
            $adzanEnd = (clone $adzanTime)->addMinutes($adzanDuration);

            if ($isFridayPrayer) {
                // Khusus Sholat Jumat: Adzan -> Khutbah & Sholat (durasi khusus misal 50 menit)
                $jumatEnd = (clone $adzanEnd)->addMinutes($jumatDuration);

                if ($now->lt($countdownStart) || $now->gte($jumatEnd)) {
                    continue;
                }

                if ($now->lt($adzanTime)) {
                    $phase = self::PHASE_COUNTDOWN;
                    $remaining = $adzanTime->timestamp - $now->timestamp;
                } elseif ($now->lt($adzanEnd)) {
                    $phase = self::PHASE_ADZAN;
                    $remaining = $adzanEnd->timestamp - $now->timestamp;
                } else {
                    $phase = self::PHASE_KHUTBAH;
                    $remaining = $jumatEnd->timestamp - $now->timestamp;
                }

                // Ambil data petugas Jumat hari ini
                $jumatPetugas = SholatJumat::whereDate('tanggal', $now->toDateString())->first()
                    ?? SholatJumat::where('tanggal', '>=', $now->copy()->startOfDay())->orderBy('tanggal')->first();

                return [
                    'active' => true,
                    'isFriday' => true,
                    'setting' => $setting,
                    'phase' => $phase,
                    'prayer' => "SHOLAT JUM'AT",
                    'currentPrayer' => (object) [
                        'nama_sholat' => "SHOLAT JUM'AT",
                        'waktu' => $item->waktu,
                    ],
                    'jumatPetugas' => $jumatPetugas,
                    'remaining' => max(0, $remaining),
                    'theme' => $theme,
                    'bgOpacity' => 80,
                    'displayMessage' => '',
                ];
            } else {
                // Hari biasa & sholat harian reguler
                $iqamahEnd = (clone $adzanEnd)->addMinutes($iqamahDuration);
                $prayerEnd = (clone $iqamahEnd)->addMinutes($prayerDuration);

                if ($now->lt($countdownStart) || $now->gte($prayerEnd)) {
                    continue;
                }

                if ($now->lt($adzanTime)) {
                    $phase = self::PHASE_COUNTDOWN;
                    $remaining = $adzanTime->timestamp - $now->timestamp;
                } elseif ($now->lt($adzanEnd)) {
                    $phase = self::PHASE_ADZAN;
                    $remaining = $adzanEnd->timestamp - $now->timestamp;
                } elseif ($now->lt($iqamahEnd)) {
                    $phase = self::PHASE_IQAMAH;
                    $remaining = $iqamahEnd->timestamp - $now->timestamp;
                } else {
                    $phase = self::PHASE_PRAYER;
                    $remaining = $prayerEnd->timestamp - $now->timestamp;
                }

                return [
                    'active' => true,
                    'isFriday' => false,
                    'setting' => $setting,
                    'phase' => $phase,
                    'prayer' => $item->nama_sholat,
                    'currentPrayer' => $item,
                    'jumatPetugas' => null,
                    'remaining' => max(0, $remaining),
                    'theme' => $theme,
                    'bgOpacity' => 80,
                    'displayMessage' => '',
                ];
            }
        }

        return [
            'active' => false,
            'isFriday' => false,
            'phase' => self::PHASE_INACTIVE,
            'setting' => $setting,
            'jumatPetugas' => null,
            'bgOpacity' => 80,
            'displayMessage' => '',
        ];
    }

    /**
     * Menampilkan halaman Mode Sholat.
     */
    public function index()
    {
        $state = $this->getPrayerState();

        $debugMode = request()->boolean('debug');

        if (!$state['active'] && !$debugMode) {
            return redirect('/');
        }

        if ($debugMode) {
            try {
                $setting = $state['setting'] ?? AppSetting::first();
            } catch (\Throwable $e) {
                $setting = null;
            }
            $debugPhase = request()->query('phase', $state['active'] ? $state['phase'] : self::PHASE_COUNTDOWN);
            $debugPrayer = request()->query('prayer', $state['active'] ? (is_object($state['currentPrayer']) ? $state['currentPrayer']->nama_sholat : $state['currentPrayer']) : 'ASHAR');
            $debugRemaining = request()->has('remaining') ? (int) request()->query('remaining') : ($state['active'] ? $state['remaining'] : ($debugPhase == 'adzan' ? 180 : ($debugPhase == 'iqamah' ? 300 : ($debugPhase == 'khutbah' ? 2700 : 18))));
            $isDebugFriday = $debugPhase == 'khutbah' || stripos($debugPrayer, 'jumat') !== false;

            $debugPetugas = null;
            if ($isDebugFriday) {
                $debugPetugas = SholatJumat::latest('tanggal')->first();
            }

            $state = [
                'active' => true,
                'isFriday' => $isDebugFriday,
                'setting' => $setting,
                'phase' => $debugPhase,
                'currentPrayer' => (object) [
                    'nama_sholat' => strtoupper($debugPrayer),
                    'waktu' => '11:55:00',
                ],
                'jumatPetugas' => $debugPetugas,
                'remaining' => $debugRemaining,
                'theme' => request()->query('theme', $setting?->prayer_mode_theme ?? 'gold'),
                'bgOpacity' => 80,
                'displayMessage' => '',
            ];
        }

        $setting = $state['setting'];

        return view('prayer-mode', [
            'setting' => $setting,
            'phase' => $state['phase'],
            'currentPrayer' => $state['currentPrayer'],
            'remainingSeconds' => $state['remaining'],
            'isFriday' => $state['isFriday'] ?? false,
            'jumatPetugas' => $state['jumatPetugas'] ?? null,
            'theme' => $state['theme'],
            'bgImage' => $state['bgImage'] ?? null,
            'bgOpacity' => $state['bgOpacity'] ?? 80,
            'displayMessage' => $state['displayMessage'] ?? '',
        ]);
    }

    /**
     * Endpoint AJAX
     * Mengembalikan status Mode Sholat.
     */
    public function status()
    {
        $debugMode = request()->boolean('debug');

        if ($debugMode) {
            $state = $this->getPrayerState();
            try {
                $setting = $state['setting'] ?? AppSetting::first();
            } catch (\Throwable $e) {
                $setting = null;
            }
            $debugPhase = request()->query('phase', $state['active'] ? $state['phase'] : self::PHASE_COUNTDOWN);
            $debugPrayer = request()->query('prayer', $state['active'] ? (is_object($state['currentPrayer']) ? $state['currentPrayer']->nama_sholat : $state['currentPrayer']) : 'ASHAR');
            $debugRemaining = request()->has('remaining') ? (int) request()->query('remaining') : ($state['active'] ? $state['remaining'] : ($debugPhase == 'adzan' ? 180 : ($debugPhase == 'iqamah' ? 300 : ($debugPhase == 'khutbah' ? 2700 : 18))));
            $isDebugFriday = $debugPhase == 'khutbah' || stripos($debugPrayer, 'jumat') !== false;

            $debugPetugas = null;
            if ($isDebugFriday) {
                $debugPetugas = SholatJumat::latest('tanggal')->first();
            }

            return response()->json([
                'active' => true,
                'isFriday' => $isDebugFriday,
                'setting' => $setting,
                'phase' => $debugPhase,
                'prayer' => strtoupper($debugPrayer),
                'currentPrayer' => (object) [
                    'nama_sholat' => strtoupper($debugPrayer),
                    'waktu' => '11:55:00',
                ],
                'jumatPetugas' => $debugPetugas,
                'remaining' => $debugRemaining,
                'theme' => request()->query('theme', $setting?->prayer_mode_theme ?? 'gold'),
                'bgOpacity' => 80,
                'displayMessage' => '',
            ]);
        }

        $state = $this->getPrayerState();
        $state['bgOpacity'] = $state['bgOpacity'] ?? 80;
        $state['displayMessage'] = $state['displayMessage'] ?? '';
        return response()->json($state);
    }
}