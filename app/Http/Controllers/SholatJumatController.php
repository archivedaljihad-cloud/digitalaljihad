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
			'foto_imam' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
			'tanggal' => 'required|date',
		]);

		// Auto-provision missing columns if not yet in database
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('bilal')->nullable()->after('muadzin');
				});
			} catch (\Throwable $e) {}
		}
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'foto_imam')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('foto_imam')->nullable()->after('bilal');
				});
			} catch (\Throwable $e) {}
		}

		$data = $request->except(['foto_imam', '_token']);

		// Guard columns if migration failed
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			unset($data['bilal']);
		}

		// Handle file upload
		if ($request->hasFile('foto_imam')) {
			$file = $request->file('foto_imam');
			$path = $file->store('sholat_jumat', 'public');
			try {
				$publicTarget = public_path('storage/' . $path);
				$publicDir = dirname($publicTarget);
				if (!file_exists($publicDir)) {
					@mkdir($publicDir, 0777, true);
				}
				@copy(storage_path('app/public/' . $path), $publicTarget);
			} catch (\Throwable $e) {}

			if (\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'foto_imam')) {
				$data['foto_imam'] = $path;
			}
		}

		SholatJumat::create($data);
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat & foto imam berhasil ditambahkan.');
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
			'foto_imam' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
			'tanggal' => 'required|date',
		]);

		// Auto-provision missing columns if not yet in database
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('bilal')->nullable()->after('muadzin');
				});
			} catch (\Throwable $e) {}
		}
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'foto_imam')) {
			try {
				\Illuminate\Support\Facades\Schema::table('sholat_jumat', function (\Illuminate\Database\Schema\Blueprint $table) {
					$table->string('foto_imam')->nullable()->after('bilal');
				});
			} catch (\Throwable $e) {}
		}

		$data = $request->except(['foto_imam', '_token', '_method', 'hapus_foto']);

		// Guard columns if migration failed
		if (!\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'bilal')) {
			unset($data['bilal']);
		}

		// Handle file upload or removal
		if ($request->hasFile('foto_imam')) {
			// Delete old photo if exists
			if (!empty($sholat_jumat->foto_imam)) {
				\Illuminate\Support\Facades\Storage::disk('public')->delete($sholat_jumat->foto_imam);
				@unlink(public_path('storage/' . $sholat_jumat->foto_imam));
			}

			$file = $request->file('foto_imam');
			$path = $file->store('sholat_jumat', 'public');
			try {
				$publicTarget = public_path('storage/' . $path);
				$publicDir = dirname($publicTarget);
				if (!file_exists($publicDir)) {
					@mkdir($publicDir, 0777, true);
				}
				@copy(storage_path('app/public/' . $path), $publicTarget);
			} catch (\Throwable $e) {}

			if (\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'foto_imam')) {
				$data['foto_imam'] = $path;
			}
		} elseif ($request->boolean('hapus_foto')) {
			if (!empty($sholat_jumat->foto_imam)) {
				\Illuminate\Support\Facades\Storage::disk('public')->delete($sholat_jumat->foto_imam);
				@unlink(public_path('storage/' . $sholat_jumat->foto_imam));
			}
			if (\Illuminate\Support\Facades\Schema::hasColumn('sholat_jumat', 'foto_imam')) {
				$data['foto_imam'] = null;
			}
		}

		$sholat_jumat->update($data);
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat & foto imam berhasil diperbarui.');
	}

	public function destroy(SholatJumat $sholat_jumat)
	{
		if (!empty($sholat_jumat->foto_imam)) {
			\Illuminate\Support\Facades\Storage::disk('public')->delete($sholat_jumat->foto_imam);
			@unlink(public_path('storage/' . $sholat_jumat->foto_imam));
		}
		$sholat_jumat->delete();
		return redirect()->route('sholat_jumat.index')->with('success', 'Jadwal sholat Jumat berhasil dihapus.');
	}
}
