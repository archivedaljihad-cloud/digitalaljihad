<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Carbon\Carbon;

class AgendaRutinController extends Controller
{
    /**
     * Halaman Pusat Pengaturan Agenda Rutin Masjid (Bisa diakses Admin & Petugas).
     */
    public function index()
    {
        $setting = AppSetting::getCached() ?? AppSetting::first() ?? new AppSetting();
        $kegiatan = $setting->getKegiatanRutin();

        return view('agenda_rutin.index', compact('setting', 'kegiatan'));
    }

    /**
     * Menyimpan pembaruan konfigurasi Agenda Rutin Masjid.
     */
    public function store(Request $request)
    {
        $setting = AppSetting::first();
        if (!$setting) {
            $setting = new AppSetting();
        }

        $existing = $setting->getKegiatanRutin();

        // 1. Data Yaasiin
        $existing['yasin']['enabled'] = $request->has('yasin_enabled');
        $existing['yasin']['judul'] = $request->input('yasin_judul', $existing['yasin']['judul']);
        $existing['yasin']['waktu'] = $request->input('yasin_waktu', $existing['yasin']['waktu']);
        $existing['yasin']['pembimbing'] = $request->input('yasin_pembimbing', $existing['yasin']['pembimbing']);
        $existing['yasin']['keterangan'] = $request->input('yasin_keterangan', $existing['yasin']['keterangan']);
        $existing['yasin']['lokasi'] = $request->input('yasin_lokasi', $existing['yasin']['lokasi']);

        // 2. Data Kajian Malam Ahad
        $existing['kajian_ahad']['enabled'] = $request->has('kajian_ahad_enabled');
        $existing['kajian_ahad']['judul'] = $request->input('kajian_ahad_judul', $existing['kajian_ahad']['judul']);
        $existing['kajian_ahad']['waktu'] = $request->input('kajian_ahad_waktu', $existing['kajian_ahad']['waktu']);
        $existing['kajian_ahad']['pembimbing'] = $request->input('kajian_ahad_pembimbing', $existing['kajian_ahad']['pembimbing']);
        $existing['kajian_ahad']['keterangan'] = $request->input('kajian_ahad_keterangan', $existing['kajian_ahad']['keterangan']);
        $existing['kajian_ahad']['lokasi'] = $request->input('kajian_ahad_lokasi', $existing['kajian_ahad']['lokasi']);

        // 3. Data Tahsin Al-Qur'an (1-Click Day Picker)
        $existing['tahsin']['enabled'] = $request->has('tahsin_enabled');
        $existing['tahsin']['judul'] = $request->input('tahsin_judul', $existing['tahsin']['judul']);
        $existing['tahsin']['waktu'] = $request->input('tahsin_waktu', $existing['tahsin']['waktu']);
        $existing['tahsin']['pembimbing'] = $request->input('tahsin_pembimbing', $existing['tahsin']['pembimbing']);
        $existing['tahsin']['keterangan'] = $request->input('tahsin_keterangan', $existing['tahsin']['keterangan']);
        $existing['tahsin']['lokasi'] = $request->input('tahsin_lokasi', $existing['tahsin']['lokasi']);
        $existing['tahsin']['catatan_khusus'] = $request->input('tahsin_catatan_khusus', '');
        
        // Array hari aktif Tahsin (misal: ['senin', 'rabu', 'sabtu'])
        $hariAktif = $request->input('tahsin_hari', []);
        $existing['tahsin']['hari_aktif'] = is_array($hariAktif) ? array_values($hariAktif) : [];

        // 4. Data Tafsir Al-Qur'an (2 Pekan Sekali)
        $existing['tafsir']['enabled'] = $request->has('tafsir_enabled');
        $existing['tafsir']['judul'] = $request->input('tafsir_judul', $existing['tafsir']['judul']);
        $existing['tafsir']['hari'] = $request->input('tafsir_hari', $existing['tafsir']['hari']);
        $existing['tafsir']['waktu'] = $request->input('tafsir_waktu', $existing['tafsir']['waktu']);
        $existing['tafsir']['pembimbing'] = $request->input('tafsir_pembimbing', $existing['tafsir']['pembimbing']);
        $existing['tafsir']['keterangan'] = $request->input('tafsir_keterangan', $existing['tafsir']['keterangan']);
        $existing['tafsir']['lokasi'] = $request->input('tafsir_lokasi', $existing['tafsir']['lokasi']);
        
        $pekanAktif = $request->input('tafsir_pekan', [1, 3]);
        $existing['tafsir']['pekan_aktif'] = array_map('intval', (array)$pekanAktif);

        $setting->kegiatan_rutin_settings = $existing;
        $setting->save();

        AppSetting::clearCache();

        return redirect()->back()->with('success', 'Jadwal Agenda Rutin Masjid berhasil diperbarui dan langsung tayang di TV Display!');
    }

    /**
     * Tampilan Slide Layar TV Digital (/agenda-rutin-embed).
     */
    public function embed()
    {
        $setting = AppSetting::getCached() ?? AppSetting::first() ?? new AppSetting();
        $kegiatan = $setting->getKegiatanRutin();

        $now = Carbon::now('Asia/Jakarta');
        $dayOfWeek = strtolower($now->locale('id')->isoFormat('dddd')); // senin, selasa, rabu, kamis, jumat, sabtu, minggu / ahad
        if ($dayOfWeek === 'minggu') $dayOfWeek = 'ahad';

        // Hitung pekan ke berapa dalam bulan ini (1 s/d 5)
        $weekOfMonth = (int) ceil($now->day / 7);

        return view('agenda-rutin-embed', compact('setting', 'kegiatan', 'now', 'dayOfWeek', 'weekOfMonth'));
    }

    /**
     * API JSON status agenda rutin untuk sinkronisasi realtime.
     */
    public function api()
    {
        $setting = AppSetting::getCached() ?? AppSetting::first() ?? new AppSetting();
        $kegiatan = $setting->getKegiatanRutin();

        $now = Carbon::now('Asia/Jakarta');
        $dayOfWeek = strtolower($now->locale('id')->isoFormat('dddd'));
        if ($dayOfWeek === 'minggu') $dayOfWeek = 'ahad';
        $weekOfMonth = (int) ceil($now->day / 7);

        return response()->json([
            'success' => true,
            'kegiatan' => $kegiatan,
            'today' => [
                'day' => $dayOfWeek,
                'date' => $now->translatedFormat('d F Y'),
                'week_of_month' => $weekOfMonth
            ]
        ]);
    }
}
