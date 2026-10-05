@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-check text-success mr-2"></i> Pusat Agenda Rutin Masjid
        </h1>
        <p class="text-muted small mb-0 mt-1">
            Kelola 4 jadwal kegiatan rutin mingguan & dwi-mingguan masjid untuk tayangan otomatis di TV Display.
        </p>
    </div>
    <div class="mt-3 mt-sm-0">
        <a href="{{ route('agenda-rutin.embed') }}" target="_blank" class="btn btn-outline-success btn-sm shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-tv mr-1"></i> Pratinjau Layar TV (/agenda-rutin-embed)
        </a>
    </div>
</div>

<!-- Alert Notifications -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form action="{{ route('agenda_rutin.store') }}" method="POST">
    @csrf

    <div class="row">
        <!-- ============================================================
             1. KEGIATAN RUTIN 1: SURAT YAASIIN (MALAM JUM'AT)
             ============================================================ -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow border-0" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #fff;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1">1</span>
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-book-quran mr-1"></i> Pembacaan Surat Yaasiin
                        </h6>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="swYasin" name="yasin_enabled" value="1" {{ ($kegiatan['yasin']['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-white" for="swYasin" style="cursor: pointer;">Tayang di TV</label>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="p-2 mb-3 rounded" style="background: #f0fdf4; border-left: 4px solid #10b981; font-size: 0.85rem;">
                        <i class="fas fa-magic text-success mr-1"></i> <strong>Sistem Otomatis:</strong> Rutin berulang setiap <strong>Malam Jum'at (Kamis malam)</strong> ba'da Maghrib. Tidak perlu diubah setiap minggu.
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small text-gray-700">Nama / Judul Kegiatan</label>
                        <input type="text" class="form-control" name="yasin_judul" value="{{ $kegiatan['yasin']['judul'] ?? 'Pembacaan Surat Yaasiin & Tahlil' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Waktu Pelaksanaan</label>
                            <input type="text" class="form-control" name="yasin_waktu" value="{{ $kegiatan['yasin']['waktu'] ?? 'Ba\'da Maghrib s/d Isya' }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Imam / Pembimbing</label>
                            <input type="text" class="form-control" name="yasin_pembimbing" value="{{ $kegiatan['yasin']['pembimbing'] ?? 'Imam Rawatib / Asatidz Masjid' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Lokasi Kegiatan</label>
                            <input type="text" class="form-control" name="yasin_lokasi" value="{{ $kegiatan['yasin']['lokasi'] ?? 'Ruang Utama Masjid Jami\' Al Jihad' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Keterangan Tambahan</label>
                            <input type="text" class="form-control" name="yasin_keterangan" value="{{ $kegiatan['yasin']['keterangan'] ?? 'Rutin Setiap Malam Jum\'at Bersama Seluruh Jamaah' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             2. KEGIATAN RUTIN 2: KAJIAN UMUM MALAM AHAD
             ============================================================ -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow border-0" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #fff;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1">2</span>
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-book-reader mr-1"></i> Kajian Malam Ahad
                        </h6>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="swAhad" name="kajian_ahad_enabled" value="1" {{ ($kegiatan['kajian_ahad']['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-white" for="swAhad" style="cursor: pointer;">Tayang di TV</label>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="p-2 mb-3 rounded" style="background: #eff6ff; border-left: 4px solid #3b82f6; font-size: 0.85rem;">
                        <i class="fas fa-magic text-primary mr-1"></i> <strong>Sistem Otomatis:</strong> Rutin berulang setiap <strong>Malam Ahad (Sabtu malam)</strong> ba'da Maghrib.
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="font-weight-bold small text-gray-700 mb-0">Nama / Judul Kajian</label>
                            <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold shadow-sm" onclick="syncKajianPekanIniBlade(true)" title="Ambil otomatis nama ustadz & tema dari jadwal kajian 1 bulan yang sedang aktif atau terdekat" style="font-size: 11px; padding: 2px 10px; border-radius: 6px;">
                                <i class="fas fa-sync-alt mr-1"></i> Ambil Jadwal Pekan Ini
                            </button>
                        </div>
                        <input type="text" class="form-control" id="bladeKajianJudul" name="kajian_ahad_judul" value="{{ $kegiatan['kajian_ahad']['judul'] ?? 'Kajian Malam Ahad' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Waktu Pelaksanaan</label>
                            <input type="text" class="form-control" id="bladeKajianWaktu" name="kajian_ahad_waktu" value="{{ $kegiatan['kajian_ahad']['waktu'] ?? 'Ba\'da Maghrib s/d Isya' }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Pemateri / Ustadz</label>
                            <input type="text" class="form-control" id="bladeKajianPembimbing" name="kajian_ahad_pembimbing" value="{{ $kegiatan['kajian_ahad']['pembimbing'] ?? '' }}" placeholder="Nama Ustadz Pemateri">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Lokasi Kegiatan</label>
                            <input type="text" class="form-control" id="bladeKajianLokasi" name="kajian_ahad_lokasi" value="{{ $kegiatan['kajian_ahad']['lokasi'] ?? 'Ruang Utama Masjid Jami\' Al Jihad' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Kitab / Pembahasan</label>
                            <input type="text" class="form-control" id="bladeKajianKet" name="kajian_ahad_keterangan" value="{{ $kegiatan['kajian_ahad']['keterangan'] ?? '' }}" placeholder="Kitab / Tema Pembahasan">
                        </div>
                    </div>
                    <div id="bladeKajianSyncStatus" class="p-1 px-2 rounded small mt-1" style="display: none; background: #e0f2fe; color: #0369a1; font-size: 11px; border-left: 3px solid #0284c7;">
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             3. KEGIATAN RUTIN 3: TAHSIN AL-QUR'AN (1-CLICK DAY PICKER)
             ============================================================ -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow border-0" style="border-radius: 14px; overflow: hidden; border: 2px solid #f59e0b !important;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #78350f 0%, #b45309 100%); color: #fff;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1">3</span>
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-quran mr-1"></i> Tahsin Al-Qur'an (Jadwal Fleksibel)
                        </h6>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="swTahsin" name="tahsin_enabled" value="1" {{ ($kegiatan['tahsin']['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-white" for="swTahsin" style="cursor: pointer;">Tayang di TV</label>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="p-3 mb-3 rounded" style="background: #fffbeb; border: 1.5px solid #fde68a;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                <i class="fas fa-hand-pointer text-warning mr-1"></i> 1-Click Day Picker (Pilih Hari Aktif Pekan Ini):
                            </span>
                            <small class="text-muted">Klik untuk pilih hari</small>
                        </div>

                        @php
                            $days = [
                                'senin' => 'Senin',
                                'selasa' => 'Selasa',
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jum\'at',
                                'sabtu' => 'Sabtu',
                                'ahad' => 'Ahad',
                            ];
                            $activeDays = $kegiatan['tahsin']['hari_aktif'] ?? ['senin', 'rabu', 'sabtu'];
                        @endphp

                        <div class="d-flex flex-wrap gap-2" style="gap: 8px;">
                            @foreach($days as $keyDay => $labelDay)
                            @php
                                $isChecked = in_array($keyDay, $activeDays);
                            @endphp
                            <label class="btn btn-sm mb-0 day-pill-btn {{ $isChecked ? 'btn-warning font-weight-bold text-dark' : 'btn-outline-secondary' }}" style="border-radius: 20px; padding: 6px 14px; cursor: pointer; transition: all 0.2s;">
                                <input type="checkbox" name="tahsin_hari[]" value="{{ $keyDay }}" {{ $isChecked ? 'checked' : '' }} style="display: none;" onchange="toggleDayPill(this)">
                                <i class="fas {{ $isChecked ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i> {{ $labelDay }}
                            </label>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 11px;">
                            * Petugas cukup mengklik tombol hari di atas jika ada perubahan hari pekan ini. Layar TV akan otomatis menyesuaikan tanggal & highlight "HARI INI".
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small text-gray-700">Nama Kegiatan</label>
                        <input type="text" class="form-control" name="tahsin_judul" value="{{ $kegiatan['tahsin']['judul'] ?? 'Bimbingan Tahsin Al-Qur\'an' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Waktu Pelaksanaan</label>
                            <input type="text" class="form-control" name="tahsin_waktu" value="{{ $kegiatan['tahsin']['waktu'] ?? 'Ba\'da Sholat Isya (20:00 WIB)' }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Pengajar / Pembimbing</label>
                            <input type="text" class="form-control" name="tahsin_pembimbing" value="{{ $kegiatan['tahsin']['pembimbing'] ?? 'Ust. Pembina Tahsin Al-Qur\'an' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Lokasi</label>
                            <input type="text" class="form-control" name="tahsin_lokasi" value="{{ $kegiatan['tahsin']['lokasi'] ?? 'Serambi & Ruang Utama Masjid' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Keterangan / Sasaran Jamaah</label>
                            <input type="text" class="form-control" name="tahsin_keterangan" value="{{ $kegiatan['tahsin']['keterangan'] ?? 'Terbuka untuk Jamaah Ikhwan & Akhwat (Semua Usia)' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             4. KEGIATAN RUTIN 4: TAFSIR AL-QUR'AN (2 PEKAN SEKALI)
             ============================================================ -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow border-0" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%); color: #fff;">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning text-dark font-weight-bold mr-2 px-2 py-1">4</span>
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-sun mr-1"></i> Kajian Umum Tafsir Al-Qur'an (2 Pekan Sekali)
                        </h6>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="swTafsir" name="tafsir_enabled" value="1" {{ ($kegiatan['tafsir']['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-white" for="swTafsir" style="cursor: pointer;">Tayang di TV</label>
                    </div>
                </div>
                <div class="card-body bg-white">
                    <div class="p-3 mb-3 rounded" style="background: #f5f3ff; border: 1.5px solid #ddd6fe;">
                        <label class="font-weight-bold text-dark small mb-2 d-block">
                            <i class="fas fa-calendar-alt text-purple mr-1"></i> Pilih Siklus 2 Pekan Sekali (Pekan Aktif):
                        </label>
                        @php
                            $pekanAktif = $kegiatan['tafsir']['pekan_aktif'] ?? [1, 3];
                        @endphp
                        <div class="d-flex flex-wrap" style="gap: 16px;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPekan1" name="tafsir_pekan[]" value="1" {{ in_array(1, $pekanAktif) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkPekan1" style="cursor: pointer;">Pekan ke-1</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPekan2" name="tafsir_pekan[]" value="2" {{ in_array(2, $pekanAktif) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkPekan2" style="cursor: pointer;">Pekan ke-2</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPekan3" name="tafsir_pekan[]" value="3" {{ in_array(3, $pekanAktif) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkPekan3" style="cursor: pointer;">Pekan ke-3</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPekan4" name="tafsir_pekan[]" value="4" {{ in_array(4, $pekanAktif) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold text-dark" for="chkPekan4" style="cursor: pointer;">Pekan ke-4</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small text-gray-700">Nama Kegiatan</label>
                        <input type="text" class="form-control" name="tafsir_judul" value="{{ $kegiatan['tafsir']['judul'] ?? 'Kajian Umum Tafsir Al-Qur\'an' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Hari Pelaksanaan</label>
                            <input type="text" class="form-control" name="tafsir_hari" value="{{ $kegiatan['tafsir']['hari'] ?? 'Ahad' }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Waktu Pelaksanaan</label>
                            <input type="text" class="form-control" name="tafsir_waktu" value="{{ $kegiatan['tafsir']['waktu'] ?? 'Ba\'da Sholat Subuh (05:15 WIB)' }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Pemateri / Ustadz</label>
                            <input type="text" class="form-control" name="tafsir_pembimbing" value="{{ $kegiatan['tafsir']['pembimbing'] ?? 'Asatidz Dewan Syari\'ah Masjid' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold small text-gray-700">Lokasi & Keterangan</label>
                            <input type="text" class="form-control" name="tafsir_keterangan" value="{{ $kegiatan['tafsir']['keterangan'] ?? 'Tafsir Ayat-ayat Pilihan & Sarapan Pagi Bersama' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOMBOL SIMPAN FIXED DI BAWAH -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
        <div class="card-body py-3 d-flex flex-column flex-sm-row align-items-center justify-content-between">
            <div class="mb-2 mb-sm-0 text-muted small">
                <i class="fas fa-info-circle text-primary mr-1"></i> Perubahan yang Anda simpan akan <strong>langsung tersinkronisasi otomatis</strong> ke layar TV digital.
            </div>
            <div>
                <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan Jadwal
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleDayPill(checkbox) {
        const parent = checkbox.closest('.day-pill-btn');
        const icon = parent.querySelector('i');
        if (checkbox.checked) {
            parent.classList.remove('btn-outline-secondary');
            parent.classList.add('btn-warning', 'font-weight-bold', 'text-dark');
            icon.className = 'fas fa-check-circle mr-1';
        } else {
            parent.classList.remove('btn-warning', 'font-weight-bold', 'text-dark');
            parent.classList.add('btn-outline-secondary');
            icon.className = 'fas fa-circle mr-1';
        }
    }

    function syncKajianPekanIniBlade(isUserClick = false) {
        try {
            const raw = localStorage.getItem('cached_kajian_sabtu');
            if (!raw) {
                if (isUserClick) alert('Belum ada data jadwal kajian 1 bulan yang tersimpan di cache. Silakan isi dan simpan di menu Kajian Malam Ahad terlebih dahulu.');
                return;
            }
            const parsed = JSON.parse(raw);
            const todayStr = (new Date()).toISOString().split('T')[0];
            let activeItem = null;
            if (Array.isArray(parsed.jadwal_list) && parsed.jadwal_list.length > 0) {
                let nearestIdx = 0;
                let minDiff = Infinity;
                parsed.jadwal_list.forEach((item, idx) => {
                    if (item.tanggal) {
                        if (item.tanggal === todayStr) {
                            nearestIdx = idx;
                            minDiff = -1;
                        } else if (item.tanggal >= todayStr && minDiff !== -1) {
                            const diff = new Date(item.tanggal) - new Date(todayStr);
                            if (diff < minDiff) {
                                minDiff = diff;
                                nearestIdx = idx;
                            }
                        }
                    }
                });
                activeItem = parsed.jadwal_list[nearestIdx] || parsed.jadwal_list[0];
            } else if (parsed.ustadz_nama) {
                activeItem = parsed;
            }

            if (!activeItem || (!activeItem.ustadz_nama && !activeItem.tema_kajian && !activeItem.kitab_rujukan)) {
                if (isUserClick) alert('Data jadwal kajian pekan ini masih kosong.');
                return;
            }

            const elWaktu = document.getElementById('bladeKajianWaktu');
            const elUstadz = document.getElementById('bladeKajianPembimbing');
            const elKet = document.getElementById('bladeKajianKet');
            const elStatus = document.getElementById('bladeKajianSyncStatus');

            const ustadzNama = (activeItem.ustadz_nama || '').trim();
            const waktuPelaksanaan = (activeItem.waktu_pelaksanaan || "Ba'da Maghrib s/d Isya").trim();

            let ketTema = '';
            if (activeItem.kitab_rujukan && activeItem.tema_kajian && activeItem.kitab_rujukan !== activeItem.tema_kajian) {
                ketTema = `${activeItem.kitab_rujukan} (${activeItem.tema_kajian})`;
            } else {
                ketTema = activeItem.kitab_rujukan || activeItem.tema_kajian || "Kitab Bidayatul Hidayah & Tanya Jawab Fiqih";
            }

            const pekanLabel = activeItem.pekan_label || (activeItem.pekan ? `Pekan ${activeItem.pekan}` : 'Pekan Aktif');

            if (elWaktu && waktuPelaksanaan) elWaktu.value = waktuPelaksanaan;
            if (elUstadz && ustadzNama) elUstadz.value = ustadzNama;
            if (elKet && ketTema) elKet.value = ketTema;

            // Highlight animasi
            if (isUserClick) {
                [elWaktu, elUstadz, elKet].forEach(el => {
                    if (el) {
                        el.style.transition = 'all 0.4s ease';
                        el.style.borderColor = '#10b981';
                        el.style.backgroundColor = '#ecfdf5';
                        setTimeout(() => {
                            el.style.borderColor = '';
                            el.style.backgroundColor = '';
                        }, 1200);
                    }
                });
            }

            if (elStatus) {
                elStatus.style.display = 'block';
                elStatus.innerHTML = `<i class="fas fa-check-circle text-success mr-1"></i> Data diambil dari <strong>${pekanLabel}</strong>: <em>${ustadzNama}</em>. Klik 'Simpan Perubahan Jadwal' di bawah untuk menyimpan.`;
            }

            if (isUserClick) {
                alert(`Alhamdulillah! Data Kajian Malam Ahad berhasil disinkronkan dari ${pekanLabel}:\n\n` +
                      `• Pemateri: ${ustadzNama}\n` +
                      `• Kitab / Tema: ${ketTema}\n` +
                      `• Waktu: ${waktuPelaksanaan}\n\n` +
                      `Silakan klik 'Simpan Perubahan Jadwal' di bagian bawah.`);
            }
        } catch(e) {
            console.warn('Gagal sinkronisasi blade:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const raw = localStorage.getItem('cached_kajian_sabtu');
            const elStatus = document.getElementById('bladeKajianSyncStatus');
            if (raw && elStatus) {
                const parsed = JSON.parse(raw);
                if (parsed.ustadz_nama || (Array.isArray(parsed.jadwal_list) && parsed.jadwal_list.length > 0)) {
                    elStatus.style.display = 'block';
                    elStatus.innerHTML = `<i class="fas fa-info-circle text-info mr-1"></i> Terhubung ke jadwal kajian 1 bulan. Klik tombol <strong>Ambil Jadwal Pekan Ini</strong> jika ingin menyamakan data.`;
                }
            }
        } catch(e) {}
    });
</script>
@endsection
