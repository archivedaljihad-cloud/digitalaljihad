public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama' => 'required|string|max:255',
        'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'keterangan' => 'nullable|string',
        'nomor_rekening' => 'nullable|string|max:100',
        'bank' => 'nullable|string|max:100',
        'atas_nama' => 'nullable|string|max:255',
        'status' => 'required|in:aktif,nonaktif'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Upload gambar
    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('qris', $filename, 'public');
    }

    $qris = Qris::create([
        'nama' => $request->nama,
        'gambar' => $path,
        'keterangan' => $request->keterangan,
        'nomor_rekening' => $request->nomor_rekening,
        'bank' => $request->bank,
        'atas_nama' => $request->atas_nama,
        'status' => $request->status
    ]);

    // Jika status yang diinput adalah 'aktif', nonaktifkan QRIS lain
    if ($qris->status === 'aktif') {
        Qris::where('id', '!=', $qris->id)
            ->update(['status' => 'nonaktif']);
    }

    return redirect()
        ->route('qris.index')
        ->with('success', 'QRIS berhasil ditambahkan!');
}