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
        // ===== Running Text Per Halaman (Opsi 3) =====
        'running_text_pages',
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
        'daily_hikmah_cache' => 'array',
        'running_text_pages' => 'array',
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

    /**
     * ==============================
     * RUNNING TEXT PER HALAMAN (OPSI 3)
     * ==============================
     */
    public function getRunningTextPages(): array
    {
        $pages = $this->running_text_pages ?? [];
        if (is_string($pages)) {
            $decoded = json_decode($pages, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($pages) ? $pages : [];
    }

    /**
     * Mengambil daftar pesan running text untuk halaman tertentu.
     * Jika halaman memiliki teks kustom, gunakan teks tersebut.
     * Jika tidak, fallback ke teks default / umum.
     */
    public function getRunningTextForPage(?string $pageKey = null): array
    {
        $pages = $this->getRunningTextPages();
        $text = null;
        $catalog = self::getDisplayPageCatalog();

        if ($pageKey) {
            $cleanKey = trim(str_replace('/', '', $pageKey));
            if (!empty($pages[$cleanKey])) {
                $text = $pages[$cleanKey];
            }
        }

        // Jika halaman belum diisi kustom oleh operator, gunakan teks hadits rekomendasi dari katalog halaman tersebut
        if (empty($text) && $pageKey) {
            $cleanKey = trim(str_replace('/', '', $pageKey));
            if (!empty($catalog[$cleanKey]['placeholder'])) {
                $text = $catalog[$cleanKey]['placeholder'];
            }
        }

        // Fallback jika masih kosong
        if (empty($text)) {
            $text = $pages['default'] ?? ($this->running_text ?? null);
        }

        $list = [];
        if (!empty($text)) {
            $lines = preg_split('/\r\n|\r|\n/', $text);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $list[] = $trimmed;
                }
            }
        }

        if (empty($list)) {
            $list = [
                "🌙 \"Luruskan dan rapatkan shaf, karena lurusnya shaf merupakan kesempurnaan sholat.\" (HR. Bukhari & Muslim)",
                "📱 Mohon menonaktifkan atau mengalihkan HP ke mode hening selama berada di dalam masjid.",
                "🤲 \"Barangsiapa membangun masjid karena Allah, maka Allah bangunkan baginya rumah di surga.\" (HR. Muslim)",
                "🧹 Jagalah selalu kebersihan dan kesucian masjid kita tercinta.",
                "💧 Hematlah dalam penggunaan air wudhu demi kelestarian bersama."
            ];
        }

        return $list;
    }

    /**
     * Master katalog halaman display beserta ikon dan deskripsi untuk panel pemetaan running text
     */
    public static function getDisplayPageCatalog(): array
    {
        return [
            'utama-embed' => [
                'name'        => 'Jadwal Sholat 5 Waktu',
                'icon'        => 'fas fa-mosque',
                'desc'        => 'Tampilan fokus jadwal sholat 5 waktu & countdown iqamah',
                'placeholder' => "Luruskan dan rapatkan shaf sholat berjamaah demi kesempurnaan sholat.\nMohon mengalihkan HP ke mode hening selama sholat berlangsung."
            ],
            'keuangan-embed' => [
                'name'        => 'Rincian Kas Masjid',
                'icon'        => 'fas fa-wallet',
                'desc'        => 'Tampilan rincian pemasukan dan pengeluaran kas utama',
                'placeholder' => "Laporan keuangan kas masjid transparan dan diaudit secara berkala.\nJazakumullah khairan katsiran kepada seluruh donatur dan jamaah."
            ],
            'keuangan-summary-embed' => [
                'name'        => 'Ringkasan Grafik Kas',
                'icon'        => 'fas fa-chart-pie',
                'desc'        => 'Ringkasan visual arus kas masjid dengan grafik donat',
                'placeholder' => "Salurkan infaq dan sedekah terbaik Anda untuk kemakmuran masjid kita tercinta."
            ],
            'jumat-embed' => [
                'name'        => 'Petugas Sholat Jumat',
                'icon'        => 'fas fa-user-tie',
                'desc'        => 'Informasi imam, khatib, muadzin & bilal sholat Jumat',
                'placeholder' => "Mohon mematikan HP dan tidak berbicara saat khatib sedang menyampaikan khutbah Jumat."
            ],
            'pengumuman-embed' => [
                'name'        => 'Pengumuman DKM',
                'icon'        => 'fas fa-bullhorn',
                'desc'        => 'Daftar warta dan agenda kegiatan masjid terkini',
                'placeholder' => "Hadirilah majelis ilmu dan kegiatan taklim rutin setiap malam ahad di masjid kita."
            ],
            'qris-embed' => [
                'name'        => 'QRIS Infaq Digital',
                'icon'        => 'fas fa-qrcode',
                'desc'        => 'Scan barcode QRIS untuk donasi dan sedekah nontunai',
                'placeholder' => "Donasi mudah, cepat, dan berkah melalui scan QRIS resmi Masjid Jami' Al-Jihad."
            ],
            'slide-embed' => [
                'name'        => 'Slide Poster Informasi',
                'icon'        => 'fas fa-images',
                'desc'        => 'Slideshow poster dakwah dan brosur kegiatan masjid',
                'placeholder' => "Simak brosur informasi dan kegiatan dakwah masjid selengkapnya di papan display."
            ],
            'ambulance-embed' => [
                'name'        => 'Kas Layanan Ambulance',
                'icon'        => 'fas fa-ambulance',
                'desc'        => 'Laporan keuangan operasional mobil ambulance masjid',
                'placeholder' => "Layanan mobil ambulance Masjid Jami' Al-Jihad siaga 24 jam melayani umat."
            ],
            'infaq-embed' => [
                'name'        => 'Program Donasi & Infaq',
                'icon'        => 'fas fa-hand-holding-heart',
                'desc'        => 'Program penggalangan dana wakaf dan renovasi masjid',
                'placeholder' => "Mari ikut serta beramal jariyah dalam program pembangunan dan fasilitas masjid."
            ],
            'hikmah-embed' => [
                'name'        => 'Mutiara Hadits & Hikmah',
                'icon'        => 'fas fa-book-open',
                'desc'        => 'Hadits shahih harian dan intisari hikmah penyejuk hati',
                'placeholder' => "Sebaik-baik manusia adalah yang paling bermanfaat bagi sesama manusia."
            ],
            'live-mekah-embed' => [
                'name'        => 'Live TV Mekah',
                'icon'        => 'fas fa-kaaba',
                'desc'        => 'Siaran langsung 24 jam Ka\'bah Masjidil Haram',
                'placeholder' => "Labbaik Allahumma Labbaik, Labbaika Laa Syariika Laka Labbaik."
            ],
            'live-madinah-embed' => [
                'name'        => 'Live TV Madinah',
                'icon'        => 'fas fa-star-and-crescent',
                'desc'        => 'Siaran langsung 24 jam Masjid Nabawi Madinah',
                'placeholder' => "Allahumma shalli 'alaa Sayyidina Muhammad wa 'alaa aali Sayyidina Muhammad."
            ],
            'idul-fitri-embed' => [
                'name'        => 'Petugas Idul Fitri',
                'icon'        => 'fas fa-moon',
                'desc'        => 'Jadwal imam, khatib, dan bilal sholat Idul Fitri',
                'placeholder' => "Taqabbalallahu minna wa minkum, Selamat Hari Raya Idul Fitri."
            ],
            'idul-adha-embed' => [
                'name'        => 'Petugas Idul Adha',
                'icon'        => 'fas fa-drum',
                'desc'        => 'Jadwal imam, khatib, dan bilal sholat Idul Adha & Qurban',
                'placeholder' => "Selamat Hari Raya Idul Adha & Ibadah Qurban, semoga membawa keberkahan."
            ],
            'welcome-embed' => [
                'name'        => 'Dashboard Lengkap',
                'icon'        => 'fas fa-desktop',
                'desc'        => 'Tampilan multi-panel ringkasan masjid',
                'placeholder' => "Selamat datang di Masjid Jami' Al-Jihad. Mari makmurkan rumah Allah."
            ],
        ];
    }
}