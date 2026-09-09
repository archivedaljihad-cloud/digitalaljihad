<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\SholatJumat;
use Illuminate\Http\Request;

class SholatJumatController extends Controller
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
		$sholatJumat = SholatJumat::where('tanggal', '>=', now()->startOfWeek())->get();
		return view('sholat_jumat.index', compact('sholatJumat'));
	}

	public function create()
	{
		return view('sholat_jumat.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'imam' => 'nullable|string|max:255',
			'khatib' => 'nullable|string|max:255',
			'muadzin' => 'nullable|string|max:255',
			'bilal' => 'nullable|string|max:255',
			'tanggal' => 'required|date',
		]);

		$data = $request->all();

		// Auto-provision or guard 'bilal' column if not yet in database
		if (isset($data['bilal']) && !\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('bilal')->nullable()->after('muadzin');
				});
			} catch (\Throwable $e) {
				unset($data['bilal']);
			}
		}

		SholatJumat::create($data);
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat berhasil ditambahkan.');
	}

	public function edit(SholatJumat $sholat_jumat)
	{
		return view('sholat_jumat.edit', compact('sholat_jumat'));
	}

	public function update(Request $request, SholatJumat $sholat_jumat)
	{
		$request->validate([
			'imam' => 'nullable|string|max:255',
			'khatib' => 'nullable|string|max:255',
			'muadzin' => 'nullable|string|max:255',
			'bilal' => 'nullable|string|max:255',
			'tanggal' => 'required|date',
		]);

		$data = $request->all();

		// Auto-provision or guard 'bilal' column if not yet in database
		if (isset($data['bilal']) && !\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('bilal')->nullable()->after('muadzin');
				});
			} catch (\Throwable $e) {
				unset($data['bilal']);
			}
		}

		$sholat_jumat->update($data);
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat berhasil diperbarui.');
	}

	public function destroy(SholatJumat $sholat_jumat)
	{
		$sholat_jumat->delete();
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat berhasil dihapus.');
	}
}
