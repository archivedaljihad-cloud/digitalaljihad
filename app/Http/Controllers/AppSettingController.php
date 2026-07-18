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
                        'active' => true,
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
            'rotation_interval'      => 'nullable|integer|min:1|max:3600',
            'rotation_enabled'       => 'sometimes|boolean',
            'rotation_pages'         => 'nullable|array',
        ]);
        $setting = $this->getOrCreateSetting();         
        $setting->nama_aplikasi = $validated['nama_aplikasi'];
        $setting->footer = $validated['footer'] ?? null;
        $setting->running_text = $validated['running_text'] ?? null;
        // Pengaturan auto update jadwal
        $setting->auto_update_jadwal = $request->boolean('auto_update_jadwal');
        $setting->auto_update_frequency = $validated['auto_update_frequency'] ?? 'daily';
        $setting->auto_update_city = $validated['auto_update_city'] ?? 'Jakarta';
        $setting->auto_update_country = $validated['auto_update_country'] ?? 'Indonesia';
        $setting->auto_update_method = $validated['auto_update_method'] ?? 11;
        if (!empty($validated['auto_update_time'])) {
            $setting->auto_update_time = $validated['auto_update_time'] . ':00';
        }
        // Pengaturan rotasi halaman
        $setting->rotation_interval = $validated['rotation_interval'] ?? 10;
        $setting->rotation_enabled = $request->boolean('rotation_enabled', true);
        if (isset($validated['rotation_pages'])) {
            $setting->rotation_pages = $validated['rotation_pages'];
        }
        // Upload file
        $this->handleFileUpload($request, 'favicon', $setting);
        $this->handleFileUpload($request, 'background', $setting);
        $this->handleFileUpload($request, 'logo', $setting);
        $setting->save();
        return redirect()
            ->route('settings.edit')
            ->with('success', 'Pengaturan aplikasi berhasil diperbarui!');
    }
    /**
     * Menangani upload file pengaturan.
     */
    protected function handleFileUpload(Request $request, string $fieldName, AppSetting $setting)
    {
        if ($request->hasFile($fieldName)) {
            if ($setting->$fieldName &&
                Storage::disk('public')->exists($setting->$fieldName)) {
                Storage::disk('public')->delete($setting->$fieldName);
            }
            $setting->$fieldName = $request
                ->file($fieldName)
                ->store('settings', 'public');
        }
    }                                 
}
