<?php
namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AppSettingController extends Controller
{
    /**
     * Mendapatkan data pengaturan atau membuat default jika belum ada.
     */
    protected function getOrCreateSetting()
    {
        return AppSetting::firstOrCreate(
            [],
            [
                'nama_aplikasi'          => "MASJID JAMI' AL JIHAD",
                'footer'                 => '2026 brought to you by DKM AL JIHAD Development Project',
                'running_text'           => 'Selamat datang di MASJID JAMI\' AL JIHAD',
                'auto_update_jadwal'     => true,
                'auto_update_frequency'  => 'daily',
                'auto_update_time'       => '00:00:00',
                'auto_update_city'       => 'Jakarta',
                'auto_update_country'    => 'Indonesia',
                'auto_update_method'     => 11,
                'rotation_interval'      => 10,
                'rotation_enabled'       => true,
                'rotation_pages'         => [
                    [
                        'url'    => 'welcome-embed',
                        'name'   => 'Dashboard Lengkap',
                        'active' => false,
                    ],
                    [
                        'url'    => 'utama-embed',
                        'name'   => 'Jadwal Sholat',
                        'active' => true,
                    ],
                    [
                        'url'    => 'keuangan-embed',
                        'name'   => 'Rincian Keuangan',
                        'active' => true,
                    ],
                    [
                        'url'    => 'jumat-embed',
                        'name'   => 'Jadwal Sholat Jumat',
                        'active' => true,
                    ],
                    [
                        'url'    => 'pengumuman-embed',
                        'name'   => 'Pengumuman',
                        'active' => true,
                    ],
                    [
                        'url'    => 'keuangan-summary-embed',
                        'name'   => 'Ringkasan Keuangan',
                        'active' => true,
                    ],
                ],
                // ==========================================
                // DEFAULT PRAYER MODE
                // ==========================================
                'prayer_mode_enabled'        => true,
                'prayer_mode_duration'       => 10,
                'prayer_mode_before_adzan'   => 5,
                'prayer_mode_adzan_duration' => 4,
                'prayer_mode_iqamah_duration'=> 10,
                'prayer_mode_after_prayer'   => 2,
                'prayer_mode_theme'          => 'gold',
            ]
        );
    }

    /**
     * Menampilkan halaman About.
     */
    public function show()
    {
        $setting = $this->getOrCreateSetting();
        return view('about', compact('setting'));
    }

    /**
     * Menampilkan halaman pengaturan.
     */
    public function edit()
    {
        $setting = $this->getOrCreateSetting();
        return view('settings.edit', compact('setting'));
    }

    /**
     * Memperbarui pengaturan aplikasi.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_aplikasi'          => 'required|string|max:255',
            'favicon'                => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:2048',
            'background'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'logo'                   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'footer'                 => 'nullable|string',
            'running_text'           => 'nullable|string',
            'auto_update_jadwal'     => 'sometimes|boolean',
            'auto_update_frequency'  => 'required_if:auto_update_jadwal,1|in:daily,weekly,monthly',
            'auto_update_time'       => 'required_if:auto_update_jadwal,1|date_format:H:i',
            'auto_update_city'       => 'required_if:auto_update_jadwal,1|string|max:255',
            'auto_update_country'    => 'required_if:auto_update_jadwal,1|string|max:255',
            'auto_update_method'     => 'required_if:auto_update_jadwal,1|integer|between:1,21',
            'live_makkah_url'        => 'nullable|string|max:500',
            'live_madinah_url'       => 'nullable|string|max:500',
            'live_stream_audio'      => 'sometimes|boolean',
            'live_stream_overlay'    => 'sometimes|boolean',
            'enable_dynamic_theme'   => 'sometimes|boolean',
            'enable_next_prayer_bar' => 'sometimes|boolean',
            'gemini_api_key'         => 'nullable|string|max:255',
            'gemini_model'           => 'nullable|string|max:100',
            'running_text_pages'     => 'nullable|array',
        ]);

        $setting = $this->getOrCreateSetting();

        // ==================================================
        // PENGATURAN UMUM & RUNNING TEXT PER HALAMAN
        // ==================================================
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('admin');
        if ($isSuperAdmin) {
            $setting->nama_aplikasi = $validated['nama_aplikasi'];
            $setting->footer = $validated['footer'] ?? null;
        }
        
        $runningTextPages = $request->input('running_text_pages', []);
        if (is_array($runningTextPages)) {
            // Bersihkan string kosong berlebih
            $sanitizedPages = [];
            foreach ($runningTextPages as $key => $val) {
                if (is_string($val)) {
                    $sanitizedPages[$key] = trim($val);
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'running_text_pages')) {
                $setting->running_text_pages = $sanitizedPages;
            }
            // Sinkronkan running_text umum ke kolom running_text (dari utama-embed atau halaman terisi pertama)
            if (!empty($sanitizedPages['utama-embed'])) {
                $setting->running_text = $sanitizedPages['utama-embed'];
            } elseif (!empty($sanitizedPages['default'])) {
                $setting->running_text = $sanitizedPages['default'];
            } else {
                $firstNonEmpty = collect($sanitizedPages)->first(fn($v) => !empty($v));
                if (!empty($firstNonEmpty)) {
                    $setting->running_text = $firstNonEmpty;
                }
            }
        }

        // ==================================================
        // AUTO UPDATE JADWAL
        // ==================================================
        $setting->auto_update_jadwal = $request->boolean('auto_update_jadwal');
        $setting->auto_update_frequency = $validated['auto_update_frequency'] ?? 'daily';
        $setting->auto_update_city = $validated['auto_update_city'] ?? 'Jakarta';
        $setting->auto_update_country = $validated['auto_update_country'] ?? 'Indonesia';
        $setting->auto_update_method = $validated['auto_update_method'] ?? 11;
        
        if (!empty($validated['auto_update_time'])) {
            $setting->auto_update_time = $validated['auto_update_time'] . ':00';
        }

        // ==================================================
        // AUTO-MIGRATE FALLBACK (JIKA KOLOM BELUM ADA DI DATABASE HOSTING)
        // ==================================================
        if (!Schema::hasColumn('app_settings', 'live_makkah_url') || !Schema::hasColumn('app_settings', 'cctv_mimbar_url')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Auto migrate in AppSettingController failed: ' . $e->getMessage());
            }
        }

        // ==================================================
        // LIVE STREAMING SETTINGS
        // ==================================================
        if (Schema::hasColumn('app_settings', 'live_makkah_url') && $request->has('live_makkah_url')) {
            $setting->live_makkah_url = $request->input('live_makkah_url');
        }
        if (Schema::hasColumn('app_settings', 'live_madinah_url') && $request->has('live_madinah_url')) {
            $setting->live_madinah_url = $request->input('live_madinah_url');
        }
        if (Schema::hasColumn('app_settings', 'live_stream_audio')) {
            $setting->live_stream_audio = $request->boolean('live_stream_audio');
        }
        if (Schema::hasColumn('app_settings', 'live_stream_overlay')) {
            $setting->live_stream_overlay = $request->boolean('live_stream_overlay');
        }

        // ==================================================
        // CCTV MIMBAR & TV OUTDOOR SETTINGS
        // ==================================================
        if (Schema::hasColumn('app_settings', 'cctv_mimbar_url') && $request->has('cctv_mimbar_url')) {
            $setting->cctv_mimbar_url = $request->input('cctv_mimbar_url');
        }
        if (Schema::hasColumn('app_settings', 'cctv_mimbar_enabled')) {
            $setting->cctv_mimbar_enabled = $request->boolean('cctv_mimbar_enabled');
        }
        if (Schema::hasColumn('app_settings', 'cctv_auto_switch_khutbah')) {
            $setting->cctv_auto_switch_khutbah = $request->boolean('cctv_auto_switch_khutbah');
        }

        // ==================================================
        // VISUAL & AMBIENT SETTINGS
        // ==================================================
        if (Schema::hasColumn('app_settings', 'enable_dynamic_theme')) {
            $setting->enable_dynamic_theme = $request->boolean('enable_dynamic_theme');
        }
        if (Schema::hasColumn('app_settings', 'enable_next_prayer_bar')) {
            $setting->enable_next_prayer_bar = $request->boolean('enable_next_prayer_bar');
        }

        // ==================================================
        // GOOGLE GEMINI AI SETTINGS
        // ==================================================
        if (Schema::hasColumn('app_settings', 'gemini_api_key') && $request->has('gemini_api_key')) {
            $setting->gemini_api_key = $request->input('gemini_api_key');
        }
        if (Schema::hasColumn('app_settings', 'gemini_model') && $request->has('gemini_model')) {
            $setting->gemini_model = $request->input('gemini_model', 'gemini-1.5-flash');
        }

        // ==================================================
        // PRAYER MODE SETTINGS
        // ==================================================
        if (Schema::hasColumn('app_settings', 'prayer_mode_enabled')) {
            $setting->prayer_mode_enabled = $request->boolean('prayer_mode_enabled');
        }
        if (Schema::hasColumn('app_settings', 'prayer_mode_duration') && $request->has('prayer_mode_duration')) {
            $setting->prayer_mode_duration = $request->input('prayer_mode_duration');
        }
        if (Schema::hasColumn('app_settings', 'prayer_mode_before_adzan') && $request->has('countdown_adzan_duration')) {
            $setting->prayer_mode_before_adzan = $request->input('countdown_adzan_duration');
        }
        if (Schema::hasColumn('app_settings', 'prayer_mode_iqamah_duration') && $request->has('iqamah_duration')) {
            $setting->prayer_mode_iqamah_duration = $request->input('iqamah_duration');
        }
        if (Schema::hasColumn('app_settings', 'tarhim_trigger_seconds') && $request->has('tarhim_trigger_seconds')) {
            $setting->tarhim_trigger_seconds = $request->input('tarhim_trigger_seconds');
        }
        if (Schema::hasColumn('app_settings', 'prayer_mode_theme') && $request->has('prayer_theme')) {
            $setting->prayer_mode_theme = $request->input('prayer_theme');
        }
        if (Schema::hasColumn('app_settings', 'prayer_bg_opacity') && $request->has('prayer_bg_opacity')) {
            $setting->prayer_bg_opacity = $request->input('prayer_bg_opacity');
        }
        if (Schema::hasColumn('app_settings', 'msg_countdown') && $request->has('msg_countdown')) {
            $setting->msg_countdown = $request->input('msg_countdown');
        }
        if (Schema::hasColumn('app_settings', 'msg_adzan') && $request->has('msg_adzan')) {
            $setting->msg_adzan = $request->input('msg_adzan');
        }
        if (Schema::hasColumn('app_settings', 'msg_iqamah') && $request->has('msg_iqamah')) {
            $setting->msg_iqamah = $request->input('msg_iqamah');
        }
        if (Schema::hasColumn('app_settings', 'msg_shalat') && $request->has('msg_shalat')) {
            $setting->msg_shalat = $request->input('msg_shalat');
        }
        if (Schema::hasColumn('app_settings', 'prayer_mode_message') && $request->has('prayer_mode_message')) {
            $setting->prayer_mode_message = $request->input('prayer_mode_message');
        }

        // ==================================================
        // UPLOAD FILE
        // ==================================================
        $this->handleFileUpload($request, 'favicon', $setting);
        $this->handleFileUpload($request, 'background', $setting);
        $this->handleFileUpload($request, 'logo', $setting);
        $this->handleFileUpload($request, 'prayer_bg_image', $setting);
        $this->handleFileUpload($request, 'tarhim_audio', $setting);
        $this->handleFileUpload($request, 'tarhim_audio_subuh', $setting);
        $this->handleFileUpload($request, 'tarhim_audio_reguler', $setting);

        $setting->save();

        return redirect()
            ->route('settings.edit')
            ->with(
                'success',
                'Pengaturan aplikasi berhasil diperbarui!'
            );
    }
    public function updatePrayerSettings(Request $request)
    {
        $validated = $request->validate([
            'rotation_interval'             => 'required|integer|min:1|max:3600',
            'prayer_mode_duration'          => 'required|integer|min:1|max:120',
            'prayer_mode_before_adzan'      => 'required|integer|min:0|max:60',
            'prayer_mode_adzan_duration'    => 'required|integer|min:1|max:60',
            'prayer_mode_iqamah_duration'   => 'required|integer|min:1|max:60',
            'prayer_mode_after_prayer'      => 'required|integer|min:0|max:60',
            'prayer_mode_jumat_duration'    => 'nullable|integer|min:10|max:180',
            'tarhim_trigger_seconds'        => 'nullable|integer|min:0|max:3600',
        ]);

        $setting = $this->getOrCreateSetting();

        $setting->rotation_interval = $validated['rotation_interval'];
        $setting->prayer_mode_duration = $validated['prayer_mode_duration'];
        $setting->prayer_mode_before_adzan = $validated['prayer_mode_before_adzan'];
        $setting->prayer_mode_adzan_duration = $validated['prayer_mode_adzan_duration'];
        $setting->prayer_mode_iqamah_duration = $validated['prayer_mode_iqamah_duration'];
        $setting->prayer_mode_after_prayer = $validated['prayer_mode_after_prayer'];
        if ($request->filled('prayer_mode_jumat_duration') && Schema::hasColumn('app_settings', 'prayer_mode_jumat_duration')) {
            $setting->prayer_mode_jumat_duration = (int) $request->input('prayer_mode_jumat_duration');
        }
        if ($request->filled('tarhim_trigger_seconds') && Schema::hasColumn('app_settings', 'tarhim_trigger_seconds')) {
            $setting->tarhim_trigger_seconds = (int) $request->input('tarhim_trigger_seconds');
        }

        $setting->save();

        \Illuminate\Support\Facades\Cache::forget('data_timestamp');

        return redirect()
            ->route('jadwal_sholat.index')
            ->with('success', 'Pengaturan waktu sistem berhasil diperbarui.');
    }

    /**
     * Menangani upload file pengaturan.
     */
    protected function handleFileUpload(
        Request $request,
        string $fieldName,
        AppSetting $setting
    ) {
        if ($request->hasFile($fieldName)) {
            if (
                $setting->$fieldName &&
                Storage::disk('public')->exists($setting->$fieldName)
            ) {
                Storage::disk('public')->delete($setting->$fieldName);
            }
            
            $setting->$fieldName = $request
                ->file($fieldName)
                ->store('settings', 'public');
        }
    }

    /**
     * Menjalankan migrasi database via aksi admin web (berguna untuk hosting Render/cPanel tanpa terminal SSH).
     */
    public function runMigration()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return redirect()
                ->route('settings.edit')
                ->with('success', 'Migrasi database berhasil dijalankan! ' . (trim($output) ?: 'Database sudah sinkron dengan versi migrasi terbaru.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('settings.edit')
                ->with('error', 'Gagal menjalankan migrasi database: ' . $e->getMessage());
        }
    }
}