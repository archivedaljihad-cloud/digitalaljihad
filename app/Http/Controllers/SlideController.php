<?php
namespace App\Http\Controllers;
use App\Models\Slide;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class SlideController extends Controller
{
    public function index()
{
    $slides = Slide::orderBy('urutan')
        ->orderBy('id')
        ->get();

    $setting = AppSetting::first();

    return view('slides.index', compact('slides', 'setting'));
}
    public function create()
    {
        return view('slides.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'urutan'    => 'required|integer|min:1',
            'durasi'    => 'required|integer|min:1|max:300',
        ]);
        DB::beginTransaction();
        try {
            $path = $request->file('gambar')->store('slides', 'public');
            Slide::create([
                'judul'     => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'gambar'    => $path,
                'urutan'    => $validated['urutan'],
                'durasi'    => $validated['durasi'],
                'aktif'     => $request->boolean('aktif'),
            ]);
            DB::commit();
            return redirect()
                ->route('slides.index')
                ->with('success', 'Slide Informasi berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan Slide Informasi: ' . $e->getMessage());
        }
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
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'urutan'    => 'required|integer|min:1',
            'durasi'    => 'required|integer|min:1|max:300',
        ]);
        DB::beginTransaction();
        try {
            $path = $slide->gambar;
            if ($request->hasFile('gambar')) {
                if (!empty($slide->gambar) &&
                    Storage::disk('public')->exists($slide->gambar)) {
                    Storage::disk('public')->delete($slide->gambar);
                }
                $path = $request->file('gambar')
                    ->store('slides', 'public');
            }
            $slide->update([
                'judul'     => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'gambar'    => $path,
                'urutan'    => $validated['urutan'],
                'durasi'    => $validated['durasi'],
                'aktif'     => $request->boolean('aktif'),
            ]);
            DB::commit();
            return redirect()
                ->route('slides.index')
                ->with('success', 'Slide Informasi berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Slide Informasi.');
        }
    }
    public function destroy($id)
    {
        $slide = Slide::findOrFail($id);
        DB::beginTransaction();
        try {
            if (!empty($slide->gambar) &&
                Storage::disk('public')->exists($slide->gambar)) {
                Storage::disk('public')->delete($slide->gambar);
            }
            $slide->delete();
            DB::commit();
            return redirect()
                ->route('slides.index')
                ->with('success', 'Slide Informasi berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->route('slides.index')
                ->with('error', 'Gagal menghapus Slide Informasi.');
        }
    }                       
}
