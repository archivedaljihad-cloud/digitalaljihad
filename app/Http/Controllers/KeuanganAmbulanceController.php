<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\KeuanganAmbulance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KeuanganAmbulanceExport;

class KeuanganAmbulanceController extends Controller
{
    protected $setting;

    public function __construct()
    {
        $this->middleware('auth')->except(['embed']);
        try {
            $this->setting = AppSetting::firstOrCreate([], [
                'nama_aplikasi' => 'Masjid Al-Ikhlas',
                'footer' => 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System',
            ]);
            view()->share('setting', $this->setting);
        } catch (\Throwable $e) {
            // ignore if database is temporarily unavailable during CLI commands
        }
    }

    public function index()
    {
        $keuangan = KeuanganAmbulance::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalPemasukan = KeuanganAmbulance::sum('pemasukan');
        $totalPengeluaran = KeuanganAmbulance::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('ambulance.index', compact(
            'keuangan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ));
    }

    public function create()
    {
        return view('ambulance.create');
    }

    /**
     * Membersihkan input rupiah dari format teks (Rp, titik ribuan, spasi, dash, dll)
     */
    private function cleanRupiah($value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $val = trim((string)$value);
        if ($val === '' || $val === '-' || strtolower($val) === 'null') {
            return 0.0;
        }

        // Hapus 'Rp', 'rp', spasi, dan karakter non-digit selain koma dan titik
        $cleaned = preg_replace('/[^\d,\.]/', '', $val);
        if ($cleaned === '') {
            return 0.0;
        }

        // Format standar Indonesia / internasional
        if (strpos($cleaned, '.') !== false && strpos($cleaned, ',') !== false) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (substr_count($cleaned, '.') > 1) {
            $cleaned = str_replace('.', '', $cleaned);
        } elseif (substr_count($cleaned, ',') > 1) {
            $cleaned = str_replace(',', '', $cleaned);
        } else {
            if (preg_match('/\.\d{3}$/', $cleaned)) {
                $cleaned = str_replace('.', '', $cleaned);
            } elseif (preg_match('/,\d{3}$/', $cleaned)) {
                $cleaned = str_replace(',', '', $cleaned);
            } else {
                $cleaned = str_replace(',', '.', $cleaned);
            }
        }

        return is_numeric($cleaned) ? (float)$cleaned : 0.0;
    }

    public function store(Request $request)
    {
        // Bersihkan dan amankan input angka rupiah
        $request->merge([
            'pemasukan'   => $this->cleanRupiah($request->input('pemasukan')),
            'pengeluaran' => $this->cleanRupiah($request->input('pengeluaran')),
        ]);

        $request->validate([
            'tanggal'      => 'required|date',
            'deskripsi'    => 'required|string|max:255',
            'pemasukan'    => 'nullable|numeric|min:0',
            'pengeluaran'  => 'nullable|numeric|min:0',
            'kategori'     => 'nullable|string|max:100',
        ], [
            'tanggal.required'    => 'Tanggal transaksi wajib diisi.',
            'deskripsi.required'  => 'Uraian / deskripsi transaksi wajib diisi.',
            'pemasukan.numeric'   => 'Nominal pemasukan harus berupa angka yang valid.',
            'pengeluaran.numeric' => 'Nominal pengeluaran harus berupa angka yang valid.',
        ]);

        $pemasukan = (float)($request->pemasukan ?? 0);
        $pengeluaran = (float)($request->pengeluaran ?? 0);

        // Ambil saldo terakhir dengan aman
        $lastSaldo = optional(
            KeuanganAmbulance::orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first()
        )->saldo ?? 0;

        $saldo = $lastSaldo + $pemasukan - $pengeluaran;

        KeuanganAmbulance::create([
            'tanggal'      => $request->tanggal,
            'deskripsi'    => $request->deskripsi,
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $saldo,
            'kategori'     => $request->kategori,
        ]);

        return redirect()
            ->route('ambulance.index')
            ->with('success', 'Data kas ambulance berhasil ditambahkan.');
    }

    public function edit(KeuanganAmbulance $ambulance)
    {
        return view('ambulance.edit', compact('ambulance'));
    }

    public function update(Request $request, KeuanganAmbulance $ambulance)
    {
        // Bersihkan dan amankan input angka rupiah
        $request->merge([
            'pemasukan'   => $this->cleanRupiah($request->input('pemasukan')),
            'pengeluaran' => $this->cleanRupiah($request->input('pengeluaran')),
        ]);

        $request->validate([
            'tanggal'      => 'required|date',
            'deskripsi'    => 'required|string|max:255',
            'pemasukan'    => 'nullable|numeric|min:0',
            'pengeluaran'  => 'nullable|numeric|min:0',
            'kategori'     => 'nullable|string|max:100',
        ], [
            'tanggal.required'    => 'Tanggal transaksi wajib diisi.',
            'deskripsi.required'  => 'Uraian / deskripsi transaksi wajib diisi.',
            'pemasukan.numeric'   => 'Nominal pemasukan harus berupa angka yang valid.',
            'pengeluaran.numeric' => 'Nominal pengeluaran harus berupa angka yang valid.',
        ]);

        $pemasukan = (float)($request->pemasukan ?? 0);
        $pengeluaran = (float)($request->pengeluaran ?? 0);

        $previousRecord = KeuanganAmbulance::where(function ($query) use ($ambulance) {
                $query->where('tanggal', '<', $ambulance->tanggal)
                      ->orWhere(function ($q) use ($ambulance) {
                          $q->where('tanggal', $ambulance->tanggal)
                            ->where('id', '<', $ambulance->id);
                      });
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $lastSaldo = $previousRecord ? $previousRecord->saldo : 0;
        $saldo = $lastSaldo + $pemasukan - $pengeluaran;

        $ambulance->update([
            'tanggal'      => $request->tanggal,
            'deskripsi'    => $request->deskripsi,
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $saldo,
            'kategori'     => $request->kategori,
        ]);

        // Hitung ulang seluruh saldo secara konsisten
        $records = KeuanganAmbulance::orderBy('tanggal')
            ->orderBy('id')
            ->get();
        $runningSaldo = 0;
        foreach ($records as $record) {
            $runningSaldo += ($record->pemasukan ?? 0) - ($record->pengeluaran ?? 0);
            if ($record->saldo != $runningSaldo) {
                $record->update([
                    'saldo' => $runningSaldo
                ]);
            }
        }

        return redirect()
            ->route('ambulance.index')
            ->with('success', 'Data kas ambulance berhasil diperbarui.');
    }

    public function destroy(KeuanganAmbulance $ambulance)
    {
        $ambulance->delete();

        // Hitung ulang seluruh saldo
        $records = KeuanganAmbulance::orderBy('tanggal')
            ->orderBy('id')
            ->get();
        $saldo = 0;
        foreach ($records as $record) {
            $saldo += ($record->pemasukan ?? 0) - ($record->pengeluaran ?? 0);
            $record->update([
                'saldo' => $saldo
            ]);
        }

        return redirect()
            ->route('ambulance.index')
            ->with('success', 'Data kas ambulance berhasil dihapus.');
    }

    public function laporan(Request $request)
    {
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));

        $keuangan = KeuanganAmbulance::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPemasukan = $keuangan->sum('pemasukan');
        $totalPengeluaran = $keuangan->sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('ambulance.laporan', compact(
            'keuangan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'dari',
            'sampai'
        ));
    }

    public function pdf(Request $request)
    {
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));

        $keuangan = KeuanganAmbulance::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPemasukan = $keuangan->sum('pemasukan');
        $totalPengeluaran = $keuangan->sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $pdf = Pdf::loadView(
            'ambulance.laporan_pdf',
            compact(
                'keuangan',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo',
                'dari',
                'sampai'
            )
        );

        return $pdf->download('laporan-kas-ambulance-' . $dari . '-' . $sampai . '.pdf');
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));

        if ($type == 'pdf') {
            return $this->pdf($request);
        }

        return Excel::download(
            new KeuanganAmbulanceExport($dari, $sampai),
            'laporan-kas-ambulance-' . $dari . '-' . $sampai . '.xlsx'
        );
    }

    /**
     * Halaman Monitor TV Layar Penuh Kas Ambulance
     */
    public function embed()
    {
        $settings = AppSetting::first();
        $keuangan = KeuanganAmbulance::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalPemasukan = KeuanganAmbulance::sum('pemasukan');
        $totalPengeluaran = KeuanganAmbulance::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view(
            'ambulance-embed',
            compact(
                'settings',
                'keuangan',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo'
            )
        );
    }
}
