<?php
namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        ]);

        $setting = $this->getOrCreateSetting();

        // ==================================================
        // PENGATURAN UMUM
        // ==================================================
        $setting->nama_aplikasi = $validated['nama_aplikasi'];
        $setting->footer = $validated['footer'] ?? null;
        $setting->running_text = $validated['running_text'] ?? null;

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
        // LIVE STREAMING SETTINGS
        // ==================================================
        if ($request->has('live_makkah_url')) {
            $setting->live_makkah_url = $request->input('live_makkah_url');
        }
        if ($request->has('live_madinah_url')) {
            $setting->live_madinah_url = $request->input('live_madinah_url');
        }
        $setting->live_stream_audio = $request->boolean('live_stream_audio');
        $setting->live_stream_overlay = $request->boolean('live_stream_overlay');

        // ==================================================
        // CCTV MIMBAR & TV OUTDOOR SETTINGS
        // ==================================================
        if ($request->has('cctv_mimbar_url')) {
            $setting->cctv_mimbar_url = $request->input('cctv_mimbar_url');
        }
        $setting->cctv_mimbar_enabled = $request->boolean('cctv_mimbar_enabled');
        $setting->cctv_auto_switch_khutbah = $request->boolean('cctv_auto_switch_khutbah');

        // ==================================================
        // VISUAL & AMBIENT SETTINGS
        // ==================================================
        $setting->enable_dynamic_theme = $request->boolean('enable_dynamic_theme');
        $setting->enable_next_prayer_bar = $request->boolean('enable_next_prayer_bar');

        // ==================================================
        // PRAYER MODE SETTINGS
        // ==================================================
        $setting->prayer_mode_enabled = $request->boolean('prayer_mode_enabled');
        if ($request->has('prayer_mode_duration')) $setting->prayer_mode_duration = $request->input('prayer_mode_duration');
        if ($request->has('countdown_adzan_duration')) $setting->prayer_mode_before_adzan = $request->input('countdown_adzan_duration');
        if ($request->has('iqamah_duration')) $setting->prayer_mode_iqamah_duration = $request->input('iqamah_duration');
        if ($request->has('tarhim_trigger_seconds')) $setting->tarhim_trigger_seconds = $request->input('tarhim_trigger_seconds');
        if ($request->has('prayer_theme')) $setting->prayer_mode_theme = $request->input('prayer_theme');
        if ($request->has('prayer_bg_opacity')) $setting->prayer_bg_opacity = $request->input('prayer_bg_opacity');
        if ($request->has('msg_countdown')) $setting->msg_countdown = $request->input('msg_countdown');
        if ($request->has('msg_adzan')) $setting->msg_adzan = $request->input('msg_adzan');
        if ($request->has('msg_iqamah')) $setting->msg_iqamah = $request->input('msg_iqamah');
        if ($request->has('msg_shalat')) $setting->msg_shalat = $request->input('msg_shalat');
        if ($request->has('prayer_mode_message')) $setting->prayer_mode_message = $request->input('prayer_mode_message');

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
        if ($request->filled('prayer_mode_jumat_duration')) {
            $setting->prayer_mode_jumat_duration = (int) $request->input('prayer_mode_jumat_duration');
        }
        if ($request->filled('tarhim_trigger_seconds')) {
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
}