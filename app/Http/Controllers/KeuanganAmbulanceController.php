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

    public function store(Request $request)
    {
        // Bersihkan titik ribuan dari input rupiah
        if ($request->has('pemasukan')) {
            $request->merge([
                'pemasukan' => str_replace(['.', ','], ['', '.'], (string)$request->pemasukan)
            ]);
        }
        if ($request->has('pengeluaran')) {
            $request->merge([
                'pengeluaran' => str_replace(['.', ','], ['', '.'], (string)$request->pengeluaran)
            ]);
        }

        $request->validate([
            'tanggal'      => 'required|date',
            'deskripsi'    => 'required|string|max:255',
            'pemasukan'    => 'nullable|numeric|min:0',
            'pengeluaran'  => 'nullable|numeric|min:0',
            'kategori'     => 'nullable|string|max:100',
        ]);

        $pemasukan = $request->pemasukan ?? 0;
        $pengeluaran = $request->pengeluaran ?? 0;

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
        // Bersihkan titik ribuan dari input rupiah
        if ($request->has('pemasukan')) {
            $request->merge([
                'pemasukan' => str_replace(['.', ','], ['', '.'], (string)$request->pemasukan)
            ]);
        }
        if ($request->has('pengeluaran')) {
            $request->merge([
                'pengeluaran' => str_replace(['.', ','], ['', '.'], (string)$request->pengeluaran)
            ]);
        }

        $request->validate([
            'tanggal'      => 'required|date',
            'deskripsi'    => 'required|string|max:255',
            'pemasukan'    => 'nullable|numeric|min:0',
            'pengeluaran'  => 'nullable|numeric|min:0',
            'kategori'     => 'nullable|string|max:100',
        ]);

        $pemasukan = $request->pemasukan ?? 0;
        $pengeluaran = $request->pengeluaran ?? 0;

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
        $totalPemasukan = KeuanganAmbulance::sum('pemasukan');
        $totalPengeluaran = KeuanganAmbulance::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $recentTransactions = KeuanganAmbulance::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view(
            'ambulance-embed',
            compact(
                'settings',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo',
                'recentTransactions'
            )
        );
    }
}
