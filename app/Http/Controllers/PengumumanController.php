<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengumumanExport;

class PengumumanController extends Controller
{

	protected $setting;

	public function __construct()
	{
		$this->middleware('auth');
		$this->setting = AppSetting::firstOrCreate([], [
			'nama_aplikasi' => 'Masjid Al-Ikhlas',
			'footer' => 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System',
            // Add other default settings if needed
		]);
		view()->share('setting', $this->setting);
	}
	
	public function index()
	{
		$pengumuman = Pengumuman::all();
		return view('pengumuman.index', compact('pengumuman'));
	}

	public function create()
	{
		return view('pengumuman.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'judul' => 'nullable|string|max:255',
			'pemateri' => 'nullable|string|max:255',
			'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
			'isi' => 'required|string',
			'tanggal' => 'required|date',
			'waktu' => 'nullable|string|max:100',
			'tempat' => 'nullable|string|max:255',
		]);

		$data = $request->only(['judul', 'pemateri', 'isi', 'tanggal', 'waktu', 'tempat']);

		if ($request->hasFile('foto')) {
			$file = $request->file('foto');
			$path = $file->store('pengumuman', 'public');
			try {
				$publicTarget = public_path('storage/' . $path);
				$publicDir = dirname($publicTarget);
				if (!file_exists($publicDir)) {
					@mkdir($publicDir, 0777, true);
				}
				@copy(storage_path('app/public/' . $path), $publicTarget);
			} catch (\Throwable $e) {}
			$data['foto'] = $path;
		}

		Pengumuman::create($data);
		return redirect()->route('pengumuman.index')->with('success', 'Pengumuman / Kegiatan berhasil ditambahkan.');
	}

	public function edit(Pengumuman $pengumuman)
	{
		return view('pengumuman.edit', compact('pengumuman'));
	}

	public function update(Request $request, Pengumuman $pengumuman)
	{
		$request->validate([
			'judul' => 'nullable|string|max:255',
			'pemateri' => 'nullable|string|max:255',
			'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
			'isi' => 'required|string',
			'tanggal' => 'required|date',
			'waktu' => 'nullable|string|max:100',
			'tempat' => 'nullable|string|max:255',
		]);

		$data = $request->only(['judul', 'pemateri', 'isi', 'tanggal', 'waktu', 'tempat']);

		if ($request->hasFile('foto')) {
			$file = $request->file('foto');
			$path = $file->store('pengumuman', 'public');
			try {
				$publicTarget = public_path('storage/' . $path);
				$publicDir = dirname($publicTarget);
				if (!file_exists($publicDir)) {
					@mkdir($publicDir, 0777, true);
				}
				@copy(storage_path('app/public/' . $path), $publicTarget);
			} catch (\Throwable $e) {}
			$data['foto'] = $path;
		}

		$pengumuman->update($data);
		return redirect()->route('pengumuman.index')->with('success', 'Pengumuman / Kegiatan berhasil diperbarui.');
	}

	public function destroy(Pengumuman $pengumuman)
	{
		$pengumuman->delete();
		return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
	}

	/**
     * Export data pengumuman ke Excel/PDF
     */
	public function export(Request $request)
	{
		$type = $request->get('type', 'excel');
		
		if ($type == 'pdf') {
			$pengumuman = Pengumuman::all();
			$pdf = Pdf::loadView('pengumuman.export_pdf', compact('pengumuman'));
			return $pdf->download('pengumuman-'.date('Y-m-d').'.pdf');
		}
		
		return Excel::download(new PengumumanExport, 'pengumuman-'.date('Y-m-d').'.xlsx');
	}
}
