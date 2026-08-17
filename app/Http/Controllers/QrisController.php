<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Qris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QrisController extends Controller
{
    protected $setting;

    public function __construct()
    {
        // Embed harus bisa diakses publik
        $this->middleware('auth')->except('embed');

        $this->setting = AppSetting::first();

        view()->share('setting', $this->setting);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qrisList = Qris::orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('qris.index', compact('qrisList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('qris.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'              => 'required|string|max:255',
            'gambar'            => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan'        => 'nullable|string',
            'nomor_rekening'    => 'nullable|string|max:100',
            'bank'              => 'nullable|string|max:100',
            'atas_nama'         => 'nullable|string|max:255',
            'status'            => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $path = null;

        try {

            DB::beginTransaction();

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();

                $path = $file->storeAs(
                    'qris',
                    $filename,
                    'public'
                );
            }

            $qris = Qris::create([
                'nama'              => $request->nama,
                'gambar'            => $path,
                'keterangan'        => $request->keterangan,
                'nomor_rekening'    => $request->nomor_rekening,
                'bank'              => $request->bank,
                'atas_nama'         => $request->atas_nama,
                'status'            => $request->status,
            ]);

            if ($qris->status === 'aktif') {
                Qris::where('id', '!=', $qris->id)
                    ->update([
                        'status' => 'nonaktif'
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('qris.index')
                ->with('success', 'QRIS berhasil ditambahkan!');
        } catch (\Exception $e) {

            DB::rollBack();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Qris $qris)
    {
        return view('qris.show', compact('qris'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Qris $qris)
    {
        return view('qris.edit', compact('qris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Qris $qris)
    {
        $validator = Validator::make($request->all(), [
            'nama'              => 'required|string|max:255',
            'gambar'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan'        => 'nullable|string',
            'nomor_rekening'    => 'nullable|string|max:100',
            'bank'              => 'nullable|string|max:100',
            'atas_nama'         => 'nullable|string|max:255',
            'status'            => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nama'              => $request->nama,
            'keterangan'        => $request->keterangan,
            'nomor_rekening'    => $request->nomor_rekening,
            'bank'              => $request->bank,
            'atas_nama'         => $request->atas_nama,
            'status'            => $request->status,
        ];

        try {

            DB::beginTransaction();

            if ($request->hasFile('gambar')) {

                if ($qris->gambar && Storage::disk('public')->exists($qris->gambar)) {
                    Storage::disk('public')->delete($qris->gambar);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();

                $data['gambar'] = $file->storeAs(
                    'qris',
                    $filename,
                    'public'
                );
            }

            $qris->update($data);

            if ($request->status === 'aktif') {
                Qris::where('id', '!=', $qris->id)
                    ->update([
                        'status' => 'nonaktif'
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('qris.index')
                ->with('success', 'QRIS berhasil diupdate!');
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Qris $qris)
    {
        if ($qris->gambar && Storage::disk('public')->exists($qris->gambar)) {
            Storage::disk('public')->delete($qris->gambar);
        }

        $qris->delete();

        return redirect()
            ->route('qris.index')
            ->with('success', 'QRIS berhasil dihapus!');
    }

    /**
     * Set QRIS menjadi aktif.
     */
    public function setAktif(Qris $qris)
    {
        DB::transaction(function () use ($qris) {

            Qris::query()->update([
                'status' => 'nonaktif'
            ]);

            $qris->update([
                'status' => 'aktif'
            ]);
        });

        return redirect()
            ->route('qris.index')
            ->with('success', 'QRIS berhasil diaktifkan!');
    }

    /**
     * Public Embed.
     */
    public function embed()
    {
        $qris = Qris::aktif()->first();

        $settings = AppSetting::first();

        return view('qris.embed', compact(
            'qris',
            'settings'
        ));
    }
}