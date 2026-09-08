<?php

namespace App\Http\Controllers;

use App\Models\ProgramInfaq;
use App\Models\DonasiInfaq;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class ProgramInfaqController extends Controller
{
    /**
     * Tampilkan halaman utama Penggalangan Infaq di Dashboard Bendahara
     */
    public function index(Request $request)
    {
        $programs = ProgramInfaq::withCount('donasi')->orderBy('id', 'desc')->get();

        $selectedProgramId = $request->query('program_id');
        if ($selectedProgramId) {
            $selectedProgram = ProgramInfaq::with('donasi')->find($selectedProgramId);
        } else {
            $selectedProgram = ProgramInfaq::where('is_active', true)->with('donasi')->first() ?? $programs->first();
        }

        $targetDana = 0;
        $totalTerkumpul = 0;
        $sisaDana = 0;
        $persentase = 0;
        $totalDonatur = 0;
        $donasiList = collect();

        if ($selectedProgram) {
            $targetDana = (float) $selectedProgram->target_dana;
            $totalTerkumpul = $selectedProgram->totalTerkumpul();
            $sisaDana = $selectedProgram->sisaDana();
            $persentase = $selectedProgram->persentase();
            $totalDonatur = $selectedProgram->totalDonatur();
            $donasiList = $selectedProgram->donasi;
        }

        return view('program_infaq.index', compact(
            'programs',
            'selectedProgram',
            'targetDana',
            'totalTerkumpul',
            'sisaDana',
            'persentase',
            'totalDonatur',
            'donasiList'
        ));
    }

    /**
     * Form tambah program baru
     */
    public function create()
    {
        return view('program_infaq.create');
    }

    /**
     * Simpan program baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_program'    => 'required|string|max:255',
            'target_dana'     => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'is_active'       => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : false;

        // Jika program pertama kali dibuat, otomatis aktifkan
        if (ProgramInfaq::count() === 0) {
            $isActive = true;
        }

        if ($isActive) {
            ProgramInfaq::query()->update(['is_active' => false]);
        }

        $program = ProgramInfaq::create([
            'nama_program'    => $validated['nama_program'],
            'target_dana'     => $validated['target_dana'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'tanggal_mulai'   => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'is_active'       => $isActive,
        ]);

        return redirect()->route('program-infaq.index', ['program_id' => $program->id])
            ->with('success', 'Program penggalangan infaq berhasil dibuat!');
    }

    /**
     * Form edit program
     */
    public function edit($id)
    {
        $program = ProgramInfaq::findOrFail($id);
        return view('program_infaq.edit', compact('program'));
    }

    /**
     * Update program
     */
    public function update(Request $request, $id)
    {
        $program = ProgramInfaq::findOrFail($id);

        $validated = $request->validate([
            'nama_program'    => 'required|string|max:255',
            'target_dana'     => 'required|numeric|min:0',
            'keterangan'      => 'nullable|string',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'is_active'       => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? (bool) $request->is_active : false;

        if ($isActive) {
            ProgramInfaq::where('id', '!=', $program->id)->update(['is_active' => false]);
        }

        $program->update([
            'nama_program'    => $validated['nama_program'],
            'target_dana'     => $validated['target_dana'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'tanggal_mulai'   => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'is_active'       => $isActive,
        ]);

        return redirect()->route('program-infaq.index', ['program_id' => $program->id])
            ->with('success', 'Program penggalangan infaq berhasil diperbarui!');
    }

    /**
     * Hapus program
     */
    public function destroy($id)
    {
        $program = ProgramInfaq::findOrFail($id);
        $wasActive = $program->is_active;
        $program->delete();

        // Jika yang dihapus aktif, aktifkan program lain yang tersisa
        if ($wasActive) {
            $other = ProgramInfaq::latest()->first();
            if ($other) {
                $other->update(['is_active' => true]);
            }
        }

        return redirect()->route('program-infaq.index')
            ->with('success', 'Program penggalangan infaq berhasil dihapus!');
    }

    /**
     * Aktifkan program untuk tampil di TV monitor
     */
    public function activate($id)
    {
        ProgramInfaq::query()->update(['is_active' => false]);
        $program = ProgramInfaq::findOrFail($id);
        $program->update(['is_active' => true]);

        return redirect()->route('program-infaq.index', ['program_id' => $program->id])
            ->with('success', 'Program "' . $program->nama_program . '" berhasil diaktifkan untuk layar TV monitor!');
    }

    /**
     * Catat donasi masuk dari donatur
     */
    public function storeDonasi(Request $request, $programId)
    {
        $program = ProgramInfaq::findOrFail($programId);

        $validated = $request->validate([
            'nama_donatur' => 'nullable|string|max:255',
            'nominal'      => 'required|numeric|min:1',
            'tanggal'      => 'required|date',
            'is_anonim'    => 'nullable|boolean',
            'keterangan'   => 'nullable|string|max:255',
        ]);

        $isAnonim = $request->has('is_anonim') ? (bool) $request->is_anonim : false;
        $namaDonatur = trim($validated['nama_donatur'] ?? '');
        if (empty($namaDonatur)) {
            $namaDonatur = 'Hamba Allah';
        }

        DonasiInfaq::create([
            'program_infaq_id' => $program->id,
            'nama_donatur'     => $namaDonatur,
            'is_anonim'        => $isAnonim,
            'nominal'          => $validated['nominal'],
            'tanggal'          => $validated['tanggal'],
            'keterangan'       => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('program-infaq.index', ['program_id' => $program->id])
            ->with('success', 'Donasi infaq sebesar Rp ' . number_format($validated['nominal'], 0, ',', '.') . ' berhasil dicatat!');
    }

    /**
     * Hapus donasi donatur
     */
    public function destroyDonasi($id)
    {
        $donasi = DonasiInfaq::findOrFail($id);
        $programId = $donasi->program_infaq_id;
        $donasi->delete();

        return redirect()->route('program-infaq.index', ['program_id' => $programId])
            ->with('success', 'Data donasi berhasil dihapus!');
    }

    /**
     * Tampilan display fullscreen untuk rotasi TV monitor
     */
    public function embed()
    {
        $settings = AppSetting::first();

        // Cari program yang berstatus aktif, jika tidak ada ambil yang terbaru
        $program = ProgramInfaq::where('is_active', true)->with('donasi')->first()
                   ?? ProgramInfaq::latest()->with('donasi')->first();

        $targetDana = 0;
        $totalTerkumpul = 0;
        $sisaDana = 0;
        $persentase = 0;
        $totalDonatur = 0;
        $donasiList = collect();

        if ($program) {
            $targetDana = (float) $program->target_dana;
            $totalTerkumpul = $program->totalTerkumpul();
            $sisaDana = $program->sisaDana();
            $persentase = $program->persentase();
            $totalDonatur = $program->totalDonatur();
            $donasiList = $program->donasi;
        }

        return view('infaq-embed', compact(
            'settings',
            'program',
            'targetDana',
            'totalTerkumpul',
            'sisaDana',
            'persentase',
            'totalDonatur',
            'donasiList'
        ));
    }
}
