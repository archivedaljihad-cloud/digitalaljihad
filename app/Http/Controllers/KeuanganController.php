<?php
namespace App\Http\Controllers;
use App\Models\AppSetting;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KeuanganExport;
class KeuanganController extends Controller
{
    protected $setting;
    public function __construct()
    {
        $this->middleware('auth');
        try {
            $this->setting = AppSetting::firstOrCreate([], [
                'nama_aplikasi' => 'Masjid Al-Ikhlas',
                'footer' => 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System',
                // Add other default settings if needed
            ]);
            view()->share('setting', $this->setting);
        } catch (\Throwable $e) {
            // ignore if database is temporarily unavailable
        }
    }
    public function index()
    {
        $keuangan = Keuangan::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalPemasukan = Keuangan::sum('pemasukan');
        $totalPengeluaran = Keuangan::sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;
        return view('keuangan.index', compact(
            'keuangan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ));
    }
    public function create()
    {
        return view('keuangan.create');
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
            Keuangan::orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first()
        )->saldo ?? 0;

        $saldo = $lastSaldo + $pemasukan - $pengeluaran;

        Keuangan::create([
            'tanggal'      => $request->tanggal,
            'deskripsi'    => $request->deskripsi,
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $saldo,
            'kategori'     => $request->kategori,
        ]);

        return redirect()
            ->route('keuangan.index')
            ->with('success', 'Data keuangan berhasil ditambahkan.');
    }

    public function edit(Keuangan $keuangan)
    {
        return view('keuangan.edit', compact('keuangan'));
    }

    public function update(Request $request, Keuangan $keuangan)
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
        // Ambil saldo sebelum transaksi yang sedang diedit
        $previousRecord = Keuangan::where(function ($query) use ($keuangan) {
                $query->where('tanggal', '<', $keuangan->tanggal)
                      ->orWhere(function ($q) use ($keuangan) {
                          $q->where('tanggal', $keuangan->tanggal)
                            ->where('id', '<', $keuangan->id);
                      });
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        $lastSaldo = $previousRecord ? $previousRecord->saldo : 0;
        $saldo = $lastSaldo + $pemasukan - $pengeluaran;
        $keuangan->update([
            'tanggal'      => $request->tanggal,
            'deskripsi'    => $request->deskripsi,
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'saldo'        => $saldo,
            'kategori'     => $request->kategori,
        ]);
        // Hitung ulang seluruh saldo
        $records = Keuangan::orderBy('tanggal')
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
            ->route('keuangan.index')
            ->with('success', 'Data keuangan berhasil diperbarui.');
    }
    public function destroy(Keuangan $keuangan)
    {
        $keuangan->delete();
        // Recalculate balances
        $records = Keuangan::orderBy('tanggal')
            ->orderBy('id')
            ->get();
        $saldo = 0;
        foreach ($records as $record) {
            $saldo = $saldo + ($record->pemasukan ?? 0) - ($record->pengeluaran ?? 0);
            $record->update([
                'saldo' => $saldo
            ]);
        }
        return redirect()
            ->route('keuangan.index')
            ->with('success', 'Data keuangan berhasil dihapus.');
    }
    /**
     * Menampilkan halaman laporan keuangan
     */
    public function laporan(Request $request)
    {
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));
        $keuangan = Keuangan::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalPemasukan = $keuangan->sum('pemasukan');
        $totalPengeluaran = $keuangan->sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;
        return view('keuangan.laporan', compact(
            'keuangan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'dari',
            'sampai'
        ));
    }
    /**
     * Export laporan keuangan ke PDF
     */
    public function pdf(Request $request)
    {
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));
        $keuangan = Keuangan::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $totalPemasukan = $keuangan->sum('pemasukan');
        $totalPengeluaran = $keuangan->sum('pengeluaran');
        $saldo = $totalPemasukan - $totalPengeluaran;
        $pdf = Pdf::loadView(
            'keuangan.laporan_pdf',
            compact(
                'keuangan',
                'totalPemasukan',
                'totalPengeluaran',
                'saldo',
                'dari',
                'sampai'
            )
        );
        return $pdf->download('laporan-keuangan-' . $dari . '-' . $sampai . '.pdf');
    }
    /**
     * Export data keuangan ke Excel
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-t'));
        if ($type == 'pdf') {
            $keuangan = Keuangan::whereBetween('tanggal', [$dari, $sampai])
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            $totalPemasukan = $keuangan->sum('pemasukan');
            $totalPengeluaran = $keuangan->sum('pengeluaran');
            $saldo = $totalPemasukan - $totalPengeluaran;
            $pdf = Pdf::loadView(
                'keuangan.laporan_pdf',
                compact(
                    'keuangan',
                    'totalPemasukan',
                    'totalPengeluaran',
                    'saldo',
                    'dari',
                    'sampai'
                )
            );
            return $pdf->download(
                'laporan-keuangan-' . $dari . '-' . $sampai . '.pdf'
            );
        }
        return Excel::download(
            new KeuanganExport($dari, $sampai),
            'laporan-keuangan-' . $dari . '-' . $sampai . '.xlsx'
        );
    }
    /**
     * Import data keuangan dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
        try {
            Excel::import(
                new \App\Imports\KeuanganImport,
                $request->file('file')
            );
            // Recalculate semua saldo setelah import
            $this->recalculateAllSaldo();
            return redirect()
                ->route('keuangan.index')
                ->with('success', 'Data keuangan berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
    /**
     * Recalculate semua saldo (private method)
     */
    private function recalculateAllSaldo()
    {
        $records = Keuangan::orderBy('tanggal')
            ->orderBy('id')
            ->get();
        $saldo = 0;
        foreach ($records as $record) {
            $saldo = $saldo
                + ($record->pemasukan ?? 0)
                - ($record->pengeluaran ?? 0);
            $record->update([
                'saldo' => $saldo
            ]);
        }
    }
}
