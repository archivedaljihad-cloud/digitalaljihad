<?php
// app/Models/AppSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'nama_aplikasi',
        'favicon',
        'background',
        'logo',
        'footer',
        'running_text',
        'auto_update_jadwal',
        'auto_update_frequency',
        'auto_update_time',
        'auto_update_city',
        'auto_update_country',
        'auto_update_method',
        'last_auto_update',
        'rotation_interval',
        'rotation_enabled',
        'rotation_pages',
        // ===== Mode Sholat =====
        'prayer_mode_enabled',
        'prayer_mode_duration',
        'prayer_mode_before_adzan',
        'prayer_mode_adzan_duration',
        'prayer_mode_iqamah_duration',
        'prayer_mode_after_prayer',
        'prayer_mode_theme',
        'prayer_mode_jumat_duration',
        'audio_tarhim',
        'tarhim_audio',
        'tarhim_audio_subuh',
        'tarhim_audio_reguler',
        'tarhim_trigger_seconds',
        // ===== Live Streaming =====
        'live_makkah_url',
        'live_madinah_url',
        'live_stream_audio',
        'live_stream_overlay',
        // ===== CCTV Mimbar & TV Luar =====
        'cctv_mimbar_url',
        'cctv_mimbar_enabled',
        'cctv_auto_switch_khutbah',
        // ===== Visual Kemewahan & Ambient =====
        'enable_dynamic_theme',
        'enable_next_prayer_bar',
        // ===== Google Gemini AI & Daily Hikmah =====
        'gemini_api_key',
        'gemini_model',
        'daily_hikmah_cache',
        'daily_hikmah_date',
    ];

    protected $casts = [
        'auto_update_jadwal' => 'boolean',
        'rotation_enabled' => 'boolean',
        'prayer_mode_enabled' => 'boolean',
        'live_stream_audio' => 'boolean',
        'live_stream_overlay' => 'boolean',
        'cctv_mimbar_enabled' => 'boolean',
        'cctv_auto_switch_khutbah' => 'boolean',
        'enable_dynamic_theme' => 'boolean',
        'enable_next_prayer_bar' => 'boolean',
        'last_auto_update' => 'datetime',
        'auto_update_time' => 'datetime:H:i:s',
        // ===== TAMBAHAN PENGATURAN AUDIO =====
        'audio_tarhim',
        'tarhim_trigger_seconds',
    ];

    public $timestamps = true;

    public function getLastAutoUpdateAttribute($value)
    {
        if ($value && !($value instanceof Carbon)) {
            try {
                return Carbon::parse($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $value;
    }

    /**
     * Daftar halaman rotasi default lengkap (14 Halaman).
     */
    public function getDefaultRotationPagesList(): array
    {
        return [
            [
                'url' => 'welcome-embed',
                'name' => 'Dashboard Lengkap',
                'active' => false
            ],
            [
                'url' => 'utama-embed',
                'name' => 'Jadwal Sholat',
                'active' => true
            ],
            [
                'url' => 'keuangan-embed',
                'name' => 'Rincian Keuangan',
                'active' => true
            ],
            [
                'url' => 'jumat-embed',
                'name' => 'Jadwal Sholat Jumat',
                'active' => true
            ],
            [
                'url' => 'pengumuman-embed',
                'name' => 'Pengumuman',
                'active' => true
            ],
            [
                'url' => 'keuangan-summary-embed',
                'name' => 'Ringkasan Keuangan',
                'active' => true
            ],
            [
                'url' => 'qris-embed',
                'name' => 'QRIS Donasi',
                'active' => true
            ],
            [
                'url' => 'slide-embed',
                'name' => 'Slide Informasi',
                'active' => true
            ],
            [
                'url' => 'idul-fitri-embed',
                'name' => 'Idul Fitri',
                'active' => true
            ],
            [
                'url' => 'idul-adha-embed',
                'name' => 'Idul Adha',
                'active' => true
            ],
            [
                'url' => 'ambulance-embed',
                'name' => 'Rincian Kas Ambulance',
                'active' => true
            ],
            [
                'url' => 'infaq-embed',
                'name' => 'Penggalangan Infaq',
                'active' => true
            ],
            [
                'url' => 'live-mekah-embed',
                'name' => 'Live TV Mekah (Masjidil Haram)',
                'active' => true
            ],
            [
                'url' => 'live-madinah-embed',
                'name' => 'Live TV Madinah (Masjid Nabawi)',
                'active' => true
            ],
            [
                'url' => 'hikmah-embed',
                'name' => 'Mutiara Hadits & Hikmah',
                'active' => true
            ]
        ];
    }

    /**
     * Daftar halaman rotasi yang sedang aktif dan tersimpan.
     */
    public function getRotationPagesList()
    {
        if (empty($this->rotation_pages)) {
            return $this->getDefaultRotationPagesList();
        }

        $data = is_string($this->rotation_pages)
            ? json_decode($this->rotation_pages, true)
            : $this->rotation_pages;

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return $this->getDefaultRotationPagesList();
        }

        // Cek halaman canonical yang mungkin belum ada di $data, tambahkan di akhir
        $existingUrls = [];
        foreach ($data as $item) {
            if (isset($item['url'])) {
                $existingUrls[] = $item['url'];
            }
        }

        foreach ($this->getDefaultRotationPagesList() as $defaultPage) {
            if (!in_array($defaultPage['url'], $existingUrls)) {
                $data[] = $defaultPage;
            }
        }

        return $data;
    }

    /**
     * Alias untuk RotationController
     */
    public function getRotationPages()
    {
        return $this->getRotationPagesList();
    }

    public function getRotationInterval()
    {
        return $this->rotation_interval ?? 10;
    }

    public function isRotationEnabled()
    {
        return $this->rotation_enabled ?? true;
    }

    /**
     * ==============================
     * MODE SHOLAT
     * ==============================
     */
    public function isPrayerModeEnabled()
    {
        return $this->prayer_mode_enabled;
    }

    public function getPrayerDuration()
    {
        return $this->prayer_mode_duration ?? 10;
    }

    public function getPrayerBeforeAdzan()
    {
        return $this->prayer_mode_before_adzan ?? 5;
    }

    public function getPrayerAdzanDuration()
    {
        return $this->prayer_mode_adzan_duration ?? 4;
    }

    public function getPrayerIqamahDuration()
    {
        return $this->prayer_mode_iqamah_duration ?? 10;
    }

    public function getPrayerAfterPrayer()
    {
        return $this->prayer_mode_after_prayer ?? 2;
    }

    public function getPrayerTheme()
    {
        return $this->prayer_mode_theme ?? 'gold';
    }

    public function getFridayPrayerDuration()
    {
        return $this->prayer_mode_jumat_duration ?? 50;
    }

    /**
     * ==============================
     * LIVE STREAMING TV
     * ==============================
     */
    public function getLiveMakkahUrl(): string
    {
        return !empty($this->live_makkah_url) 
            ? $this->live_makkah_url 
            : 'https://www.youtube.com/watch?v=live_stream?channel=UCr_yW_8sC_Yg_U9b_wH5Npg';
    }

    public function getLiveMadinahUrl(): string
    {
        return !empty($this->live_madinah_url) 
            ? $this->live_madinah_url 
            : 'https://www.youtube.com/watch?v=live_stream?channel=UCaT_20Vp2Zq0FzXp3m4bVrg';
    }

    public function isLiveStreamAudioEnabled(): bool
    {
        return (bool) ($this->live_stream_audio ?? false);
    }

    public function isLiveStreamOverlayEnabled(): bool
    {
        return (bool) ($this->live_stream_overlay ?? true);
    }

    /**
     * ==============================
     * AMBIENT THEME & NEXT PRAYER BAR
     * ==============================
     */
    public function isDynamicThemeEnabled(): bool
    {
        return (bool) ($this->enable_dynamic_theme ?? true);
    }

    public function isNextPrayerBarEnabled(): bool
    {
        return (bool) ($this->enable_next_prayer_bar ?? true);
    }

    /**
     * ==============================
     * CCTV MIMBAR & TV OUTDOOR
     * ==============================
     */
    public function getCctvMimbarUrl(): string
    {
        return (string) ($this->cctv_mimbar_url ?? '');
    }

    public function isCctvMimbarEnabled(): bool
    {
        return (bool) ($this->cctv_mimbar_enabled ?? false);
    }

    public function isCctvAutoSwitchKhutbah(): bool
    {
        return (bool) ($this->cctv_auto_switch_khutbah ?? true);
    }
}