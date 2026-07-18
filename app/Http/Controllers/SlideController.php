<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::orderBy('urutan')->get();

        return view('slides.index', compact('slides'));
    }

    public function create()
    {
        return view('slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:255',
            'deskripsi' => 'nullable',
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'urutan' => 'required|integer',
        ]);

        $namaFile = time() . '.' . $request->gambar->extension();

        $request->gambar->move(
            public_path('uploads/slides'),
            $namaFile
        );

        Slide::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $namaFile,
            'urutan' => $request->urutan,
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()
            ->route('slides.index')
            ->with('success', 'Slide berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        return redirect()->route('slides.index');
    }

    public function edit($id)
    {
        $slide = Slide::findOrFail($id);

        return view('slides.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $slide = Slide::findOrFail($id);

        $request->validate([
            'judul' => 'required|max:255',
            'deskripsi' => 'nullable',
            'urutan' => 'required|integer',
        ]);

        if ($request->hasFile('gambar')) {

            if (
                $slide->gambar &&
                file_exists(public_path('uploads/slides/' . $slide->gambar))
            ) {
                unlink(public_path('uploads/slides/' . $slide->gambar));
            }

            $namaFile = time() . '.' . $request->gambar->extension();

            $request->gambar->move(
                public_path('uploads/slides'),
                $namaFile
            );

            $slide->gambar = $namaFile;
        }

        $slide->judul = $request->judul;
        $slide->deskripsi = $request->deskripsi;
        $slide->urutan = $request->urutan;
        $slide->aktif = $request->has('aktif');

        $slide->save();

        return redirect()
            ->route('slides.index')
            ->with('success', 'Slide berhasil diupdate.');
    }

    public function destroy($id)
    {
        $slide = Slide::findOrFail($id);

        if (
            $slide->gambar &&
            file_exists(public_path('uploads/slides/' . $slide->gambar))
        ) {
            unlink(public_path('uploads/slides/' . $slide->gambar));
        }

        $slide->delete();

        return redirect()
            ->route('slides.index')
            ->with('success', 'Slide berhasil dihapus.');
    }
}