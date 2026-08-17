<?php

namespace App\Http\Controllers;

use App\Models\SholatIdulAdha;
use Illuminate\Http\Request;

class SholatIdulAdhaController extends Controller
{
    public function index()
      {
    $sholatIdulAdha = SholatIdulAdha::orderBy('tahun', 'desc')->get();

    return view('idul-adha.index', compact('sholatIdulAdha'));
   }

    public function create()
    {
        return view('idul-adha.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'      => 'required|integer|min:2020|max:2100|unique:sholat_idul_adha,tahun',
            'tanggal'    => 'required|date',
            'imam'       => 'nullable|string|max:255',
            'khatib'     => 'nullable|string|max:255',
            'muadzin'    => 'nullable|string|max:255',
            'waktu'      => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        SholatIdulAdha::create($validated);

        return redirect()
            ->route('idul-adha.index')
            ->with('success', 'Jadwal Sholat Idul Adha berhasil ditambahkan!');
    }

    public function edit(SholatIdulAdha $idulAdha)
    {
        return view('idul-adha.edit', compact('idulAdha'));
    }

    public function update(Request $request, SholatIdulAdha $idulAdha)
    {
        $validated = $request->validate([
            'tahun'      => 'required|integer|min:2020|max:2100|unique:sholat_idul_adha,tahun,' . $idulAdha->id,
            'tanggal'    => 'required|date',
            'imam'       => 'nullable|string|max:255',
            'khatib'     => 'nullable|string|max:255',
            'muadzin'    => 'nullable|string|max:255',
            'waktu'      => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $idulAdha->update($validated);

        return redirect()
            ->route('idul-adha.index')
            ->with('success', 'Jadwal Sholat Idul Adha berhasil diupdate!');
    }

    public function destroy(SholatIdulAdha $idulAdha)
    {
        $idulAdha->delete();

        return redirect()
            ->route('idul-adha.index')
            ->with('success', 'Jadwal Sholat Idul Adha berhasil dihapus!');
    }

    public function embed()
    {
        $settings = \App\Models\AppSetting::first();
        $idulAdha = SholatIdulAdha::orderBy('tahun', 'desc')->first();

        return view('idul-adha-embed', compact('settings', 'idulAdha'));
    }
}