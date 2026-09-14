<!-- Modal AI Pengumuman (Google Gemini) -->
<div class="modal fade" id="aiModal" tabindex="-1" role="dialog" aria-labelledby="aiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #071a10 0%, #0e3521 50%, #1e5a3a 100%); border-bottom: 2px solid #c9a03d;">
                <div class="d-flex align-items-center">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(201,160,61,0.2); color: #ffd700; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="aiModalLabel" style="font-size: 17px;">
                            Asisten AI Pembuat Pengumuman Masjid
                        </h5>
                        <small class="text-light opacity-80">Didukung oleh Google Gemini AI</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="background: #fdfdfd;">
                <!-- Input Poin Mentah -->
                <div class="form-group mb-3">
                    <label class="font-weight-bold" style="color: #0e3521;">
                        <i class="fas fa-pen-alt mr-1 text-success"></i> Masukkan Poin-Poin Singkat Kegiatan:
                    </label>
                    <textarea id="aiPointsInput" class="form-control" rows="3" 
                        placeholder="Contoh: Kajian ba'da maghrib sabtu ustadz Fulan Lc tema mendidik anak di era gadget. Ada makan malam bersama jamaah." 
                        style="border-radius: 12px; border: 1.5px solid rgba(30,90,58,0.2); font-size: 14px;"></textarea>
                    <small class="text-muted">Cukup ketik poin mentah, AI akan merangkum judul, waktu, dan kalimat pengumuman resmi islami yang santun.</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="font-weight-bold small" style="color: #0e3521;">Kategori Acara:</label>
                        <select id="aiCategorySelect" class="form-control form-control-sm" style="border-radius: 8px;">
                            <option value="Kajian Rutin">Kajian Rutin & Taklim</option>
                            <option value="Peringatan Hari Besar Islam">Peringatan Hari Besar Islam (PHBI)</option>
                            <option value="Bakti Sosial & Santunan">Bakti Sosial & Santunan Kaum Dhuafa</option>
                            <option value="Kerja Bakti & Kebersihan">Kerja Bakti Kebersihan Masjid</option>
                            <option value="Sholat Gerhana / Istisqo">Sholat Khusus (Gerhana / Istisqo)</option>
                            <option value="Pengumuman Umum DKM">Pengumuman Umum DKM</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end mt-3 mt-md-0">
                        <button type="button" id="btnGenerateAi" class="btn btn-block text-white font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #1e5a3a, #0e3521); border: 1px solid #c9a03d; border-radius: 10px; padding: 8px 16px;">
                            <i class="fas fa-sparkles mr-1 text-warning"></i> ✨ Susun dengan AI
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="aiLoadingState" class="text-center py-4 d-none">
                    <div class="spinner-border text-success mb-2" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                    <div class="font-weight-bold text-success">Google Gemini sedang menyusun draf pengumuman...</div>
                    <small class="text-muted">Memilihkan kalimat ajakan santun dan merapikan format acara</small>
                </div>

                <!-- Error Alert -->
                <div id="aiErrorAlert" class="alert alert-danger d-none" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <span id="aiErrorMessage">Gagal menyusun pengumuman.</span>
                </div>

                <!-- Result Preview Card -->
                <div id="aiResultCard" class="card border-0 shadow-sm d-none mt-3" style="background: #ffffff; border: 1px solid rgba(201,160,61,0.3) !important; border-radius: 14px;">
                    <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center" style="background: rgba(30,90,58,0.06); border-radius: 14px 14px 0 0;">
                        <span class="small font-weight-bold" style="color: #0e3521;">
                            <i class="fas fa-check-circle text-success mr-1"></i> Hasil Draf AI (Siap Diterapkan)
                        </span>
                        <span id="aiBadgeSource" class="badge badge-success font-weight-normal" style="font-size: 11px;">Gemini AI</span>
                    </div>
                    <div class="card-body p-3 small" style="line-height: 1.6;">
                        <div class="mb-2">
                            <strong style="color: #0e3521;">Judul:</strong>
                            <div id="aiResJudul" class="p-2 rounded bg-light border font-weight-bold mt-1"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong style="color: #0e3521;">Waktu:</strong>
                                <div id="aiResWaktu" class="p-1 px-2 rounded bg-light border mt-1"></div>
                            </div>
                            <div class="col-6">
                                <strong style="color: #0e3521;">Pemateri:</strong>
                                <div id="aiResPemateri" class="p-1 px-2 rounded bg-light border mt-1"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <strong style="color: #0e3521;">Isi Deskripsi Pengumuman:</strong>
                            <div id="aiResIsi" class="p-2 rounded bg-light border mt-1 text-justify" style="white-space: pre-line; max-height: 140px; overflow-y: auto;"></div>
                        </div>
                        <div>
                            <strong style="color: #0e3521;">Ringkasan Running Text TV:</strong>
                            <div id="aiResRunning" class="p-2 rounded bg-light border mt-1 text-muted"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                <button type="button" id="btnApplyAi" class="btn btn-success btn-sm px-4 font-weight-bold d-none" style="border-radius: 8px; background: #1e5a3a; border-color: #1e5a3a;">
                    <i class="fas fa-arrow-down mr-1"></i> Terapkan ke Form
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnGenerate = document.getElementById('btnGenerateAi');
    const btnApply = document.getElementById('btnApplyAi');
    const pointsInput = document.getElementById('aiPointsInput');
    const categorySelect = document.getElementById('aiCategorySelect');
    const loadingState = document.getElementById('aiLoadingState');
    const errorAlert = document.getElementById('aiErrorAlert');
    const errorMsg = document.getElementById('aiErrorMessage');
    const resultCard = document.getElementById('aiResultCard');

    let currentAiData = null;

    if (!btnGenerate) return;

    btnGenerate.addEventListener('click', function () {
        const poin = pointsInput.value.trim();
        if (!poin) {
            alert('Mohon tuliskan poin-poin kegiatan terlebih dahulu.');
            pointsInput.focus();
            return;
        }

        loadingState.classList.remove('d-none');
        errorAlert.classList.add('d-none');
        resultCard.classList.add('d-none');
        btnApply.classList.add('d-none');
        btnGenerate.disabled = true;

        fetch("{{ route('ai.generate-pengumuman') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                poin: poin,
                kategori: categorySelect.value
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Terjadi kesalahan jaringan atau server (HTTP ' + response.status + ')');
            }
            return response.json();
        })
        .then(res => {
            loadingState.classList.add('d-none');
            btnGenerate.disabled = false;

            if (res.success && res.data) {
                currentAiData = res.data;
                document.getElementById('aiResJudul').textContent = res.data.judul || '-';
                document.getElementById('aiResWaktu').textContent = res.data.waktu || '-';
                document.getElementById('aiResPemateri').textContent = res.data.pemateri || '-';
                document.getElementById('aiResIsi').textContent = res.data.isi || '-';
                document.getElementById('aiResRunning').textContent = res.data.running_text || '-';
                
                const badgeSource = document.getElementById('aiBadgeSource');
                if (res.source === 'gemini_ai') {
                    badgeSource.className = 'badge badge-success font-weight-normal';
                    badgeSource.textContent = 'Google Gemini AI';
                } else {
                    badgeSource.className = 'badge badge-secondary font-weight-normal';
                    badgeSource.textContent = 'Template Cerdas';
                }

                resultCard.classList.remove('d-none');
                btnApply.classList.remove('d-none');
            } else {
                errorAlert.classList.remove('d-none');
                errorMsg.textContent = res.message || 'Gagal menyusun pengumuman.';
            }
        })
        .catch(err => {
            loadingState.classList.add('d-none');
            btnGenerate.disabled = false;
            errorAlert.classList.remove('d-none');
            errorMsg.textContent = err.message || 'Gagal menghubungi server.';
        });
    });

    btnApply.addEventListener('click', function () {
        if (!currentAiData) return;

        const elJudul = document.getElementById('judul');
        const elPemateri = document.getElementById('pemateri');
        const elWaktu = document.getElementById('waktu');
        const elTempat = document.getElementById('tempat');
        const elIsi = document.getElementById('isi');

        if (elJudul && currentAiData.judul) elJudul.value = currentAiData.judul;
        if (elPemateri && currentAiData.pemateri) elPemateri.value = currentAiData.pemateri;
        if (elWaktu && currentAiData.waktu) elWaktu.value = currentAiData.waktu;
        if (elTempat && currentAiData.tempat) elTempat.value = currentAiData.tempat;
        if (elIsi && currentAiData.isi) {
            elIsi.value = currentAiData.isi;
            const charCount = document.getElementById('charCount');
            if (charCount) charCount.textContent = currentAiData.isi.length;
        }

        // Close modal
        if (typeof $ !== 'undefined') {
            $('#aiModal').modal('hide');
        }
    });
});
</script>
