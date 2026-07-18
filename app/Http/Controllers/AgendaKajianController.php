<?php

namespace App\Http\Controllers;

use App\Models\AgendaKajian;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AgendaKajianController extends Controller
{
    protected $setting;

    public function __construct()
    {
        $this->middleware('auth');

        $this->setting = AppSetting::firstOrCreate([], [
            'nama_aplikasi' => "MASJID JAMI' AL JIHAD",
            'footer' => '2026 brought to you by DKM AL JIHAD Development Project',
        ]);

        view()->share('setting', $this->setting);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agenda = AgendaKajian::urut()->get();

        return view('agenda_kajian.index', compact('agenda'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('agenda_kajian.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|max:255',
            'pemateri'   => 'required|max:255',
            'tanggal'    => 'required|date',
            'waktu'      => 'required',
            'lokasi'     => 'nullable|max:255',
            'deskripsi'  => 'nullable',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan'     => 'required|integer',
        ]);

        $namaFile = null;

        if ($request->hasFile('gambar')) {

            $namaFile = time() . '.' . $request->gambar->extension();

            $request->gambar->move(
                public_path('uploads/agenda'),
                $namaFile
            );
        }

        AgendaKajian::create([
            'judul'      => $request->judul,
            'pemateri'   => $request->pemateri,
            'tanggal'    => $request->tanggal,
            'waktu'      => $request->waktu,
            'lokasi'     => $request->lokasi,
            'deskripsi'  => $request->deskripsi,
            'gambar'     => $namaFile,
            'urutan'     => $request->urutan,
            'aktif'      => $request->has('aktif'),
        ]);

        return redirect()
            ->route('agenda_kajian.index')
            ->with('success', 'Agenda kajian berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('agenda_kajian.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agenda = AgendaKajian::findOrFail($id);

        return view('agenda_kajian.edit', compact('agenda'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $agenda = AgendaKajian::findOrFail($id);

        $request->validate([
            'judul'      => 'required|max:255',
            'pemateri'   => 'required|max:255',
            'tanggal'    => 'required|date',
            'waktu'      => 'required',
            'lokasi'     => 'nullable|max:255',
            'deskripsi'  => 'nullable',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan'     => 'required|integer',
        ]);

        if ($request->hasFile('gambar')) {

            if (
                $agenda->gambar &&
                file_exists(public_path('uploads/agenda/' . $agenda->gambar))
            ) {
                unlink(public_path('uploads/agenda/' . $agenda->gambar));
            }

            $namaFile = time() . '.' . $request->gambar->extension();

            $request->gambar->move(
                public_path('uploads/agenda'),
                $namaFile
            );

            $agenda->gambar = $namaFile;
        }

        $agenda->judul = $request->judul;
        $agenda->pemateri = $request->pemateri;
        $agenda->tanggal = $request->tanggal;
        $agenda->waktu = $request->waktu;
        $agenda->lokasi = $request->lokasi;
        $agenda->deskripsi = $request->deskripsi;
        $agenda->urutan = $request->urutan;
        $agenda->aktif = $request->has('aktif');

        $agenda->save();

        return redirect()
            ->route('agenda_kajian.index')
            ->with('success', 'Agenda kajian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id)
    {
        $agenda = AgendaKajian::findOrFail($id);

        if (
            $agenda->gambar &&
            file_exists(public_path('uploads/agenda/' . $agenda->gambar))
        ) {
            unlink(public_path('uploads/agenda/' . $agenda->gambar));
        }

        $agenda->delete();

        return redirect()
            ->route('agenda_kajian.index')
            ->with('success', 'Agenda kajian berhasil dihapus.');
    }
}