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
        'audio_tarhim',
        'tarhim_audio',
        'tarhim_audio_subuh',
        'tarhim_audio_reguler',
        'tarhim_trigger_seconds',
    ];

    protected $casts = [
        'auto_update_jadwal' => 'boolean',
        'rotation_enabled' => 'boolean',
        'prayer_mode_enabled' => 'boolean',
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
     * Daftar halaman rotasi default.
     */
    public function getRotationPagesList()
    {
        if (empty($this->rotation_pages)) {
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
                ]
            ];
        }

        $data = is_string($this->rotation_pages)
            ? json_decode($this->rotation_pages, true)
            : $this->rotation_pages;

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
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
                ]
            ];
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
}