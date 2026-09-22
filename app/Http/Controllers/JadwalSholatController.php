<?php
namespace App\Http\Controllers;
use App\Exports\JadwalSholatExport;
use App\Models\AppSetting;
use App\Models\JadwalSholat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class JadwalSholatController extends Controller
{
    /**
     * Data pengaturan aplikasi.
     *
     * @var \App\Models\AppSetting|null
     */
    protected $setting;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->setting = AppSetting::getCached() ?? new AppSetting();
        view()->share('setting', $this->setting);
    }
    /**
     * Menampilkan daftar jadwal sholat.
     */
    public function index()
    {
        $jadwal = JadwalSholat::urutkan()->get();
        $setting = $this->setting ?? AppSetting::getCached() ?? new AppSetting();
        return view('jadwal_sholat.index', compact('jadwal', 'setting'));
    }
    /**
     * Menampilkan form tambah jadwal sholat.
     */
    public function create()
    {
        return view('jadwal_sholat.create');
    }
    /**
     * Menyimpan data jadwal sholat.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sholat' => 'required|string|max:255',
            'waktu'       => 'required',
        ]);
        JadwalSholat::create($validated);
        return redirect()
            ->route('jadwal_sholat.index')
            ->with('success', 'Jadwal sholat berhasil ditambahkan.');
    }  
    /**
     * Menampilkan form edit jadwal sholat.
     */
    public function edit(JadwalSholat $jadwal_sholat)
    {
        return view('jadwal_sholat.edit', compact('jadwal_sholat'));
    }
    /**
     * Memperbarui data jadwal sholat.
     */
    public function update(Request $request, JadwalSholat $jadwal_sholat)
    {
        $validated = $request->validate([
            'nama_sholat' => 'required|string|max:255',
            'waktu'       => 'required',
        ]);
        $jadwal_sholat->update($validated);
        return redirect()
            ->route('jadwal_sholat.index')
            ->with('success', 'Jadwal sholat berhasil diperbarui.');
    }
    /**
     * Menghapus data jadwal sholat.
     */
    public function destroy(JadwalSholat $jadwal_sholat)
    {
        $jadwal_sholat->delete();
        return redirect()
            ->route('jadwal_sholat.index')
            ->with('success', 'Jadwal sholat berhasil dihapus.');
    }
    /**
     * Export data jadwal sholat ke Excel atau PDF.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        if ($type === 'pdf') {
            $jadwal = JadwalSholat::urutkan()->get();
            $pdf = Pdf::loadView(
                'jadwal_sholat.export_pdf',
                compact('jadwal')
            );
            return $pdf->download(
                'jadwal-sholat-' . now()->format('Y-m-d') . '.pdf'
            );
        }
        return Excel::download(
            new JadwalSholatExport(),
            'jadwal-sholat-' . now()->format('Y-m-d') . '.xlsx'
        );
    } 
}
