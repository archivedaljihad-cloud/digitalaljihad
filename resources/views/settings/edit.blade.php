{{-- resources/views/settings/edit.blade.php --}}
@extends('layouts.admin')

@section('main-content')
	<div class="container-fluid">
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<div>
				<h1 class="h3 mb-0 text-gray-800 font-weight-bold">
					@if(auth()->check() && (auth()->user()->hasRole('petugas') || !auth()->user()->hasRole('admin')))
						<i class="fas fa-bullhorn text-warning mr-2"></i> Pengaturan Teks Berjalan & Tampilan TV
					@else
						<i class="fas fa-cogs text-primary mr-2"></i> Pengaturan Aplikasi
					@endif
				</h1>
				@if(auth()->check() && auth()->user()->hasRole('petugas'))
					<small class="text-muted d-block mt-1">
						<span class="badge badge-info mr-1">Akses Operator</span> Anda memiliki hak akses penuh untuk memperbarui teks berjalan utama dan teks berjalan khusus tiap halaman display TV.
					</small>
				@endif
			</div>
			@if(auth()->check() && auth()->user()->hasRole('admin'))
			<a href="{{ route('settings.migrate') }}" class="btn btn-sm btn-info shadow-sm" onclick="return confirm('Jalankan migrasi database sekarang untuk menyinkronkan seluruh tabel & kolom terbaru?')">
				<i class="fas fa-database fa-sm text-white-50 mr-1"></i> Sinkronkan Database (Migrate)
			</a>
			@endif
		</div>

		@if (session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		@endif

		@if (session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		@endif

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul class="mb-0">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<ul class="nav nav-tabs card-header-tabs" id="settingsTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
							<i class="fas fa-cog"></i> Umum
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="prayermode-tab" data-toggle="tab" href="#prayermode" role="tab" style="color: #ffd700;">
							<i class="fas fa-mosque" style="color: #ffd700;"></i> Prayer Mode (5 Waktu & Jum'at)
						</a>					
					</li>
					<li class="nav-item">
						<a class="nav-link" id="autoupdate-tab" data-toggle="tab" href="#autoupdate" role="tab" style="color: #ffd700;">
							<i class="fas fa-sync-alt" style="color: #ffd700;"></i> Auto-Update Jadwal
							@if($setting->auto_update_jadwal)
								<span class="badge badge-success ml-2">AKTIF</span>
							@else
								<span class="badge badge-secondary ml-2">NONAKTIF</span>
							@endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="livetv-tab" data-toggle="tab" href="#livetv" role="tab" style="color: #ffd700;">
							<i class="fas fa-video" style="color: #ffd700;"></i> Live TV Streaming
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="cctvmimbar-tab" data-toggle="tab" href="#cctvmimbar" role="tab" style="color: #10b981;">
							<i class="fas fa-camera text-success"></i> CCTV Mimbar & TV Luar
							@if($setting->cctv_mimbar_enabled ?? false)
								<span class="badge badge-success ml-1">AKTIF</span>
							@endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="ai-tab" data-toggle="tab" href="#ai" role="tab" style="color: #4f46e5;">
							<i class="fas fa-magic" style="color: #ffd700;"></i> Google Gemini AI
							@if(!empty($setting->gemini_api_key))
								<span class="badge badge-success ml-1">AKTIF</span>
							@else
								<span class="badge badge-warning ml-1">KUNCI BELUM DIISI</span>
							@endif
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="yasin-tab" data-toggle="tab" href="#yasin" role="tab" style="color: #059669;">
							<i class="fas fa-book-quran text-success"></i> Agenda Malam Jum'at
							@if($setting->yasin_mode_enabled ?? true)
								<span class="badge badge-success ml-1">AKTIF</span>
							@else
								<span class="badge badge-secondary ml-1">NONAKTIF</span>
							@endif
						</a>
					</li>
				</ul>
			</div>
			<div class="card-body">
				<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
					@csrf
					@method('PUT')

					<div class="tab-content" id="settingsTabContent">
						{{-- TAB UMUM --}}
						<div class="tab-pane fade show active" id="general" role="tabpanel">
							@php
								$isAdmin = auth()->check() && auth()->user()->hasRole('admin');
							@endphp

							@if($isAdmin)
							<div class="row">
								{{-- NAMA APLIKASI (KOLOM KIRI) --}}
								<div class="col-md-6">
									<div class="form-group">
										<label for="nama_aplikasi" class="font-weight-bold">
											Nama Aplikasi <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi"
											value="{{ old('nama_aplikasi', $setting->nama_aplikasi ?? '') }}" required>
									</div>
								</div>

								{{-- FOOTER TEXT (KOLOM KANAN - 1 LINE SEJAJAR) --}}
								<div class="col-md-6">
									<div class="form-group">
										<label for="footer" class="font-weight-bold">
											Footer Text
										</label>
										<input type="text" class="form-control" id="footer" name="footer"
											value="{{ old('footer', $setting->footer ?? '') }}"
											placeholder="Contoh: © 2026 Powered by DKM AL JIHAD">
									</div>
								</div>
							</div>
							@else
								{{-- Nilai Default untuk Operator (Disembunyikan agar form tetap bersih, fokus, dan bebas gangguan) --}}
								<input type="hidden" name="nama_aplikasi" value="{{ $setting->nama_aplikasi ?? '' }}">
								<input type="hidden" name="footer" value="{{ $setting->footer ?? '' }}">
							@endif

							{{-- ======================================================== --}}
							{{-- PANEL PENGATURAN TEKS BERJALAN TIAP HALAMAN DISPLAY TV --}}
							{{-- ======================================================== --}}
							@php
								$runningTextPages = method_exists($setting, 'getRunningTextPages') ? $setting->getRunningTextPages() : ($setting->running_text_pages ?? []);
								if (is_string($runningTextPages)) {
									$runningTextPages = json_decode($runningTextPages, true) ?? [];
								}
								$pageCatalog = \App\Models\AppSetting::getDisplayPageCatalog();
							@endphp

							<div class="row">
								<div class="col-12 mt-2">
									{{-- ======================================================== --}}
									{{-- PANEL PENGATURAN TEKS BERJALAN TIAP HALAMAN DISPLAY TV --}}
									{{-- ======================================================== --}}
									<div class="card border-0 shadow-sm mt-1 mb-4" style="background: linear-gradient(135deg, rgba(7,26,16,0.03) 0%, rgba(14,53,33,0.07) 100%); border: 1.5px solid rgba(26,82,53,0.2) !important; border-radius: 14px;">
										<div class="card-body p-4">
											<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
												<div>
													<h6 class="font-weight-bold text-success mb-1" style="font-size: 1.08rem;">
														<i class="fas fa-bullhorn text-warning mr-2"></i> Pengaturan Teks Berjalan Tiap Halaman Display TV
													</h6>
													<small class="text-muted">
														Atur kalimat teks berjalan di bagian bawah layar yang spesifik dan selaras untuk masing-masing halaman display TV (hadits sholat, hadits sedekah, adab khutbah Jumat, dll). Tekan <strong>ENTER</strong> untuk membuat kalimat berikutnya bergantian. Halaman yang dikosongkan otomatis memakai teks hadits rekomendasi bawaan.
													</small>
												</div>
												<div class="mt-2 mt-md-0">
													<button type="button" class="btn btn-sm btn-outline-success font-weight-bold" id="btnToggleAllRunningPages">
														<i class="fas fa-expand-arrows-alt mr-1"></i> Buka / Tutup Semua Panel
													</button>
												</div>
											</div>

											<div class="accordion" id="accordionRunningPages">
												@foreach($pageCatalog as $pageSlug => $pageMeta)
													@php
														$pageVal = old("running_text_pages.{$pageSlug}", $runningTextPages[$pageSlug] ?? '');
														$hasCustom = !empty(trim($pageVal));
														$collapseId = 'collapse_' . str_replace(['-', '.'], '_', $pageSlug);
													@endphp
													<div class="card mb-2 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1.5px solid {{ $hasCustom ? '#10b981' : 'rgba(212,175,55,0.3)' }} !important;">
														<div class="card-header py-2 px-3 d-flex justify-content-between align-items-center" 
															style="cursor: pointer; user-select: none; background: linear-gradient(135deg, #071a10 0%, #0e3521 100%) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important;" 
															data-toggle="collapse" 
															data-target="#{{ $collapseId }}">
															<div class="d-flex align-items-center">
																<span style="width: 38px; height: 38px; min-width: 38px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: {{ $hasCustom ? '#10b981' : 'rgba(255,255,255,0.12)' }} !important; color: {{ $hasCustom ? '#ffffff' : '#ffd700' }} !important; border: 1.5px solid {{ $hasCustom ? '#34d399' : 'rgba(255,255,255,0.25)' }} !important; font-size: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.3);" class="mr-3">
																	<i class="{{ $pageMeta['icon'] ?? 'fas fa-tv' }}"></i>
																</span>
																<div>
																	<div class="d-flex align-items-center flex-wrap">
																		<strong style="color: #ffffff !important; font-size: 1.02rem; font-weight: 700; letter-spacing: 0.3px; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pageMeta['name'] }}</strong>
																		<span class="badge ml-2" style="background: rgba(255, 215, 0, 0.18) !important; color: #ffd700 !important; border: 1px solid rgba(255, 215, 0, 0.45) !important; font-size: 0.76rem; font-family: monospace; font-weight: 600;">/{{ $pageSlug }}</span>
																	</div>
																	<small style="color: #cbd5e1 !important; font-size: 0.83rem; display: block; margin-top: 2px;">{{ $pageMeta['desc'] }}</small>
																</div>
															</div>
															<div class="d-flex align-items-center">
																@if($hasCustom)
																	<span class="badge px-2 py-1 mr-2" style="background: #10b981 !important; color: #ffffff !important; font-weight: 700; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-check-circle mr-1"></i> Teks Kustom</span>
																@else
																	<span class="badge px-2 py-1 mr-2" style="background: rgba(255, 255, 255, 0.14) !important; color: #f1f5f9 !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-weight: 600; font-size: 0.8rem;"><i class="fas fa-book-open mr-1"></i> Rekomendasi Bawaan</span>
																@endif
																<i class="fas fa-chevron-down" style="color: #ffd700 !important; font-size: 0.95rem;"></i>
															</div>
														</div>

														<div id="{{ $collapseId }}" class="collapse {{ $hasCustom ? 'show' : '' }}">
															<div class="card-body py-3 px-4" style="background: #ffffff !important; border-top: 1px solid #e2e8f0 !important;">
																<div class="form-group mb-0">
																	<label class="small font-weight-bold d-flex justify-content-between" style="color: #0f172a !important; font-size: 0.9rem;">
																		<span>Kalimat Teks Berjalan Khusus untuk Layar <strong>{{ $pageMeta['name'] }}</strong>:</span>
																		<span class="text-muted font-weight-normal"><i class="fas fa-level-down-alt fa-rotate-90"></i> Tekan Enter = pesan berikutnya bergantian</span>
																	</label>
																	<textarea class="form-control" 
																		name="running_text_pages[{{ $pageSlug }}]" 
																		rows="2" 
																		style="background: #ffffff !important; color: #0f172a !important; border: 1.5px solid #cbd5e1 !important; font-size: 0.92rem; border-radius: 8px;"
																		placeholder="{{ $pageMeta['placeholder'] }}">{{ $pageVal }}</textarea>
																	<small class="text-muted d-block mt-1">
																		<i class="fas fa-lightbulb text-warning"></i> Kosongkan kolom ini jika ingin halaman ini otomatis memakai teks hadits rekomendasi bawaan.
																	</small>
																</div>
															</div>
														</div>
													</div>
												@endforeach
											</div>
										</div>
									</div>
								</div>
							</div>

							@if($isAdmin)
							<hr class="my-4">

							{{-- MEDIA & GAMBAR (FAVICON, LOGO, BACKGROUND) TERTATA RAPI SEJAJAR (KHUSUS ADMINISTRATOR) --}}
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="favicon" class="font-weight-bold">Favicon</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="favicon" name="favicon"
												accept=".ico,.png,.jpg,.jpeg,.gif">
											<label class="custom-file-label" for="favicon">Pilih file favicon</label>
										</div>
										@if($setting->favicon)
											<div class="mt-3">
												<p class="mb-1 small font-weight-bold">Favicon Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->favicon) }}" width="48" height="48"
													class="img-thumbnail d-block">
												<small class="text-muted">Rekomendasi: 64x64 px (.ico / .png)</small>
											</div>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="logo" class="font-weight-bold">Logo Aplikasi</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="logo" name="logo"
												accept=".png,.jpg,.jpeg,.gif,.svg">
											<label class="custom-file-label" for="logo">Pilih file logo</label>
										</div>
										@if($setting->logo)
											<div class="mt-3">
												<p class="mb-1 small font-weight-bold">Logo Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->logo) }}" class="img-thumbnail"
													style="max-height: 120px; width: auto;">
												<small class="text-muted d-block">Rekomendasi: maks 300x150 px (.png)</small>
											</div>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="background" class="font-weight-bold">Background Sidebar</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="background" name="background"
												accept=".jpg,.jpeg,.png,.gif">
											<label class="custom-file-label" for="background">Pilih file background</label>
										</div>
										@if($setting->background)
											<div class="mt-3">
												<p class="mb-1 small font-weight-bold">Background Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->background) }}" class="img-thumbnail"
													style="max-height: 120px; width: 100%; object-fit: cover;">
												<small class="text-muted d-block">Rekomendasi: 1920x1080 px</small>
											</div>
										@endif
									</div>
								</div>
							</div>
							@endif

							<hr class="my-4">

							{{-- PENGATURAN TAMPILAN MEWAH & AMBIENT (SMART DISPLAY) --}}
							<div class="row">
								<div class="col-md-12">
									<h5 class="mb-3 text-primary font-weight-bold">
										<i class="fas fa-gem text-warning mr-1"></i> Tampilan Visual Mewah & Interaktif (Smart Display)
									</h5>
								</div>

								<div class="col-md-6">
									<div class="card bg-light border-0 shadow-sm mb-3">
										<div class="card-body">
											<div class="custom-control custom-switch">
												<input type="hidden" name="enable_dynamic_theme" value="0">
												<input type="checkbox" class="custom-control-input" id="enable_dynamic_theme"
													name="enable_dynamic_theme" value="1" {{ old('enable_dynamic_theme', $setting->enable_dynamic_theme ?? 1) ? 'checked' : '' }}>
												<label class="custom-control-label font-weight-bold" for="enable_dynamic_theme">
													<i class="fas fa-palette text-info mr-1"></i> Dynamic Ambient Theme Sesuai Waktu Sholat
												</label>
											</div>
											<small class="text-muted d-block mt-2">
												Warna latar belakang dan pencahayaan pendaran aura (ambient orbs) berubah dinamis dan halus mengikuti siklus 6 waktu sholat: Subuh (Biru Fajar & Emas), Dhuha (Hijau Emerald Segar), Dzuhur (Radiant Teal), Ashar (Amber Keemasan), Maghrib (Violet Twilight Senja), dan Isya (Midnight Sapphire).
											</small>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="card bg-light border-0 shadow-sm mb-3">
										<div class="card-body">
											<div class="custom-control custom-switch">
												<input type="hidden" name="enable_next_prayer_bar" value="0">
												<input type="checkbox" class="custom-control-input" id="enable_next_prayer_bar"
													name="enable_next_prayer_bar" value="1" {{ old('enable_next_prayer_bar', $setting->enable_next_prayer_bar ?? 1) ? 'checked' : '' }}>
												<label class="custom-control-label font-weight-bold" for="enable_next_prayer_bar">
													<i class="fas fa-stopwatch text-warning mr-1"></i> Floating Smart Next Prayer Bar
												</label>
											</div>
											<small class="text-muted d-block mt-2">
												Menampilkan kapsul kaca mewah mengambang (glassmorphic floating widget) di pojok kanan atas layar TV yang menghitung mundur waktu menuju sholat berikutnya secara detik-demi-detik, dan otomatis sembunyi saat mode sholat berlangsung.
											</small>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB PRAYER MODE (JUM'AT & REGULER) --}}
						<div class="tab-pane fade" id="prayermode" role="tabpanel">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info border-left-info shadow-sm">
										<i class="fas fa-info-circle mr-1"></i>
										<strong>Pengaturan Terpusat Prayer Mode (Sholat 5 Waktu & Jum'at):</strong> Atur aktivasi mode sholat otomatis, durasi hitung mundur sebelum adzan, durasi saat adzan, durasi iqamah, durasi sholat fardhu 5 waktu, durasi khusus Sholat Jum'at, audio tarhim, serta pesan-pesan layar TV.
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<div class="custom-control custom-switch mt-2 mb-4">
											<input type="hidden" name="prayer_mode_enabled" value="0">
											<input type="checkbox" class="custom-control-input" id="prayer_mode_enabled"
												name="prayer_mode_enabled" value="1" {{ old('prayer_mode_enabled', $setting->prayer_mode_enabled ?? 1) ? 'checked' : '' }}>
											<label class="custom-control-label" for="prayer_mode_enabled">
												<strong>Aktifkan Prayer Mode Otomatis</strong>
											</label>
										</div>
									</div>

									<h6 class="font-weight-bold text-success mb-3 border-bottom pb-2">
										<i class="fas fa-stopwatch mr-1"></i> Pengaturan Waktu & Durasi Mode Sholat
									</h6>
									
									<div class="form-group">
										<label class="font-weight-bold">Durasi Countdown Sebelum Adzan (Menit)</label>
										<input type="number" name="prayer_mode_before_adzan" class="form-control" value="{{ old('prayer_mode_before_adzan', $setting->prayer_mode_before_adzan ?? 5) }}" min="1">
										<small class="text-muted">Layar TV mulai menghitung mundur sekian menit sebelum adzan tiba.</small>
									</div>

									<div class="form-group">
										<label class="font-weight-bold">Durasi Adzan (Menit)</label>
										<input type="number" name="prayer_mode_adzan_duration" class="form-control" value="{{ old('prayer_mode_adzan_duration', $setting->prayer_mode_adzan_duration ?? 4) }}" min="1">
										<small class="text-muted">Durasi tampilan saat adzan berkumandang.</small>
									</div>

									<div class="form-group">
										<label class="font-weight-bold">Durasi Iqamah (Menit)</label>
										<input type="number" name="prayer_mode_iqamah_duration" class="form-control" value="{{ old('prayer_mode_iqamah_duration', $setting->prayer_mode_iqamah_duration ?? 10) }}" min="1">
										<small class="text-muted">Hitung mundur iqamah menuju sholat berjamaah.</small>
									</div>

									<div class="form-group">
										<label class="font-weight-bold">Durasi Sholat Keseluruhan / Reguler (Menit)</label>
										<input type="number" name="prayer_mode_duration" class="form-control" value="{{ old('prayer_mode_duration', $setting->prayer_mode_duration ?? 10) }}" min="1">
										<small class="text-muted">Durasi layar TV terkunci hening saat sholat 5 waktu berlangsung.</small>
									</div>

									{{-- DURASI KHUSUS HARI JUMAT --}}
									<div class="form-group bg-light p-3 rounded border border-success mb-3">
										<label class="font-weight-bold text-success mb-1">
											<i class="fas fa-mosque mr-1"></i> Durasi Sholat Jum'at (Khutbah & Sholat Berjamaah)
										</label>
										<div class="input-group">
											<input type="number" name="prayer_mode_jumat_duration" class="form-control" value="{{ old('prayer_mode_jumat_duration', $setting->prayer_mode_jumat_duration ?? 50) }}" min="10" max="180">
											<div class="input-group-append">
												<span class="input-group-text">menit</span>
											</div>
										</div>
										<small class="text-muted d-block mt-1">Durasi khusus hari Jum'at di waktu Dzuhur (TV terkunci tenang menampilkan kartu petugas & hadits adab khutbah).</small>
									</div>

									<div class="form-group">
										<label for="tarhim_trigger_minutes" class="font-weight-bold">Waktu Mulai Audio Tarhim Sebelum Adzan</label>
										@php
											$currentTriggerSec = old('tarhim_trigger_seconds', $setting->tarhim_trigger_seconds ?? 300);
											$currentTriggerMin = round($currentTriggerSec / 60);
										@endphp
										<select name="tarhim_trigger_minutes" id="tarhim_trigger_minutes" class="form-control" onchange="document.getElementById('tarhim_trigger_seconds').value = this.value * 60;">
											<option value="5" {{ $currentTriggerMin == 5 ? 'selected' : '' }}>5 Menit Sebelum Adzan (Standar)</option>
											<option value="10" {{ $currentTriggerMin == 10 ? 'selected' : '' }}>10 Menit Sebelum Adzan</option>
											<option value="15" {{ $currentTriggerMin == 15 ? 'selected' : '' }}>15 Menit Sebelum Adzan</option>
										</select>
										<input type="hidden" name="tarhim_trigger_seconds" id="tarhim_trigger_seconds" value="{{ $currentTriggerSec }}">
										<small class="text-muted">Audio Tarhim akan otomatis berbunyi sesuai durasi menit yang dipilih sebelum adzan tiba.</small>
									</div>

									{{-- KOLOM PILIH FILE AUDIO TARHIM SUBUH (PANJANG / LENGKAP) --}}
									<div class="form-group">
										<label for="tarhim_audio_subuh" class="font-weight-bold">
											<i class="fas fa-moon text-primary mr-1"></i> File Audio Tarhim Subuh (Lengkap / Panjang)
										</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="tarhim_audio_subuh" name="tarhim_audio_subuh" accept=".mp3,.wav,.ogg">
											<label class="custom-file-label" for="tarhim_audio_subuh">Pilih audio tarhim Subuh</label>
										</div>
										@if(!empty($setting->tarhim_audio_subuh))
											<div class="mt-2">
												<small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Audio Tarhim Subuh khusus sudah terpasang.</small>
											</div>
										@else
											<div class="mt-1">
												<small class="text-muted">Jika kosong, sistem menggunakan audio default sistem (Subuh.mp3/tarhim panjang).</small>
											</div>
										@endif
									</div>

									{{-- KOLOM PILIH FILE AUDIO TARHIM REGULER (PENDEK / SELAIN SUBUH) --}}
									<div class="form-group">
										<label for="tarhim_audio_reguler" class="font-weight-bold">
											<i class="fas fa-sun text-warning mr-1"></i> File Audio Tarhim Reguler (Pendek / Dzuhur, Ashar, Maghrib, Isya)
										</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="tarhim_audio_reguler" name="tarhim_audio_reguler" accept=".mp3,.wav,.ogg">
											<label class="custom-file-label" for="tarhim_audio_reguler">Pilih audio tarhim reguler</label>
										</div>
										@if(!empty($setting->tarhim_audio_reguler))
											<div class="mt-2">
												<small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Audio Tarhim Reguler sudah terpasang.</small>
											</div>
										@elseif(!empty($setting->tarhim_audio))
											<div class="mt-2">
												<small class="text-info font-weight-bold"><i class="fas fa-check-circle"></i> Menggunakan file audio tarhim umum yang sudah diunggah sebelumnya.</small>
											</div>
										@else
											<div class="mt-1">
												<small class="text-muted">Jika kosong, sistem menggunakan audio tarhim standar pendek (tarhim2.mp3).</small>
											</div>
										@endif
									</div>
									
									<h6 class="font-weight-bold text-success mb-3 mt-4 border-bottom pb-2">Pengaturan Tampilan</h6>
									
									<div class="form-group">
										<label>Tema Tampilan (Background Solid)</label>
										<select name="prayer_theme" class="form-control">
											<option value="default" {{ (old('prayer_theme', $setting->prayer_theme ?? 'default') == 'default') ? 'selected' : '' }}>Hijau Gelap (Default)</option>
											<option value="dark" {{ (old('prayer_theme', $setting->prayer_theme ?? '') == 'dark') ? 'selected' : '' }}>Hitam Pekat (Dark)</option>
											<option value="light" {{ (old('prayer_theme', $setting->prayer_theme ?? '') == 'light') ? 'selected' : '' }}>Terang (Light)</option>
										</select>
									</div>

									<div class="form-group">
										<label>Atau Pilih Background Image Baru</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="prayer_bg_image" name="prayer_bg_image"
												accept=".jpg,.jpeg,.png">
											<label class="custom-file-label" for="prayer_bg_image">Pilih file image</label>
										</div>
										@if(!empty($setting->prayer_bg_image))
											<div class="mt-2">
												<small class="text-muted">Background saat ini terpasang.</small>
											</div>
										@endif
									</div>

									<div class="form-group">
										<label>Tingkat Keredupan Background (%)</label>
										<input type="range" name="prayer_bg_opacity" class="form-control-range" min="0" max="100" value="{{ old('prayer_bg_opacity', $setting->prayer_bg_opacity ?? 80) }}" oninput="this.nextElementSibling.value = this.value + '%'">
										<output class="font-weight-bold text-primary">{{ old('prayer_bg_opacity', $setting->prayer_bg_opacity ?? 80) }}%</output>
									</div>
								</div>

								<div class="col-md-6">
									<h6 class="font-weight-bold text-success mb-3 border-bottom pb-2">Pengaturan Pesan (Teks)</h6>
									
									<div class="form-group">
										<label>Pesan Saat Countdown Adzan</label>
										<textarea name="msg_countdown" class="form-control" rows="2">{{ old('msg_countdown', $setting->msg_countdown ?? 'Bersiap Masuk Waktu Sholat') }}</textarea>
									</div>

									<div class="form-group">
										<label>Pesan Saat Berkumandang Adzan</label>
										<textarea name="msg_adzan" class="form-control" rows="2">{{ old('msg_adzan', $setting->msg_adzan ?? 'Waktu Adzan Telah Tiba') }}</textarea>
									</div>

									<div class="form-group">
										<label>Pesan Saat Hitung Mundur Iqamah</label>
										<textarea name="msg_iqamah" class="form-control" rows="2">{{ old('msg_iqamah', $setting->msg_iqamah ?? 'Menuju Waktu Iqamah') }}</textarea>
									</div>

									<div class="form-group">
										<label>Pesan Saat Sholat Berlangsung</label>
										<textarea name="msg_shalat" class="form-control" rows="2">{{ old('msg_shalat', $setting->msg_shalat ?? 'Luruskan & Rapatkan Shaf. Matikan Alat Komunikasi.') }}</textarea>
									</div>
									
									<div class="form-group">
										<label>Pesan Mode Sholat (Lama / Cadangan)</label>
										<textarea name="prayer_mode_message" class="form-control" rows="2">{{ old('prayer_mode_message', $setting->prayer_mode_message ?? 'Harap Tenang') }}</textarea>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB AUTO-UPDATE --}}
						<div class="tab-pane fade" id="autoupdate" role="tabpanel">
							<div class="row">
								<div class="col-lg-8">
									<div class="form-group">
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="auto_update_jadwal"
												name="auto_update_jadwal" value="1" {{ $setting->auto_update_jadwal ? 'checked' : '' }}>
											<label class="custom-control-label" for="auto_update_jadwal">
												<strong>Aktifkan Auto-Update Jadwal Sholat</strong>
											</label>
										</div>
										<small class="form-text text-muted">
											Jika diaktifkan, jadwal sholat akan diperbarui secara otomatis dari API
											eksternal
										</small>
									</div>

									<div id="autoUpdateOptions"
										style="{{ $setting->auto_update_jadwal ? '' : 'display: none;' }}">
										<div class="form-group">
											<label for="auto_update_frequency">Frekuensi Update</label>
											<select class="form-control" id="auto_update_frequency"
												name="auto_update_frequency">
												<option value="daily" {{ ($setting->auto_update_frequency ?? 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
												<option value="weekly" {{ ($setting->auto_update_frequency ?? '') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
												<option value="monthly" {{ ($setting->auto_update_frequency ?? '') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
											</select>
											<small class="form-text text-muted">Frekuensi pembaruan jadwal sholat</small>
										</div>

										<div class="form-group">
											<label for="auto_update_time">Waktu Update</label>
											<input type="text" class="form-control timepicker-24" id="auto_update_time"
												name="auto_update_time"
												value="{{ substr($setting->auto_update_time ?? '00:00:00', 0, 5) }}" placeholder="Format 24 Jam (Contoh: 00:00)">
											<small class="form-text text-muted">Waktu ketika sistem akan melakukan update
												otomatis</small>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="auto_update_city">Kota</label>
													<input type="text" class="form-control" id="auto_update_city"
														name="auto_update_city"
														value="{{ $setting->auto_update_city ?? 'Jakarta' }}"
														placeholder="Contoh: Jakarta" required>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="auto_update_country">Negara</label>
													<input type="text" class="form-control" id="auto_update_country"
														name="auto_update_country"
														value="{{ $setting->auto_update_country ?? 'Indonesia' }}"
														placeholder="Contoh: Indonesia" required>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label for="auto_update_method">Metode Perhitungan</label>
											<select class="form-control" id="auto_update_method" name="auto_update_method">
												<option value="11" {{ ($setting->auto_update_method ?? 11) == 11 ? 'selected' : '' }}>Kementerian Agama RI (Metode 11)</option>
												<option value="20" {{ ($setting->auto_update_method ?? 11) == 20 ? 'selected' : '' }}>Kementerian Agama RI (Metode 20)</option>
												<option value="1" {{ ($setting->auto_update_method ?? 11) == 1 ? 'selected' : '' }}>University of Islamic Sciences, Karachi</option>
												<option value="2" {{ ($setting->auto_update_method ?? 11) == 2 ? 'selected' : '' }}>Islamic Society of North America</option>
												<option value="3" {{ ($setting->auto_update_method ?? 11) == 3 ? 'selected' : '' }}>Muslim World League</option>
												<option value="4" {{ ($setting->auto_update_method ?? 11) == 4 ? 'selected' : '' }}>Umm Al-Qura University, Makkah</option>
												<option value="5" {{ ($setting->auto_update_method ?? 11) == 5 ? 'selected' : '' }}>Egyptian General Authority of Survey</option>
											</select>
											<small class="form-text text-muted">Metode perhitungan waktu sholat yang
												digunakan</small>
										</div>

										<div class="alert alert-info">
											<i class="fas fa-info-circle"></i>
											<strong>Informasi:</strong> Jadwal sholat akan diupdate secara otomatis
											berdasarkan lokasi
											<strong>{{ $setting->auto_update_city ?? 'Jakarta' }},
												{{ $setting->auto_update_country ?? 'Indonesia' }}</strong>
											dengan metode yang dipilih. Pastikan koneksi internet stabil.
										</div>

										@if($setting->last_auto_update)
											<div class="alert alert-secondary">
												<i class="fas fa-clock"></i>
												<strong>Terakhir update:</strong>
												{{ \Carbon\Carbon::parse($setting->last_auto_update)->format('d M Y H:i:s') }}
											</div>
										@endif
									</div>
								</div>

								<div class="col-lg-4">
									<div class="card shadow-sm">
										<div class="card-header bg-info text-white">
											<i class="fas fa-info-circle"></i> Informasi Auto-Update
										</div>
										<div class="card-body">
											<p><strong>Status:</strong>
												@if($setting->auto_update_jadwal)
													<span class="badge badge-success">Aktif</span>
												@else
													<span class="badge badge-danger">Nonaktif</span>
												@endif
											</p>
											<p><strong>Frekuensi:</strong>
												{{ ucfirst($setting->auto_update_frequency ?? 'daily') }}</p>
											<p><strong>Waktu:</strong>
												{{ substr($setting->auto_update_time ?? '00:00:00', 0, 5) }} WIB</p>
											<p><strong>Lokasi:</strong> {{ $setting->auto_update_city ?? 'Jakarta' }},
												{{ $setting->auto_update_country ?? 'Indonesia' }}</p>
											<hr>
											<p class="mb-0"><small>Data diambil dari API Aladhan.com</small></p>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB LIVE TV STREAMING --}}
						<div class="tab-pane fade" id="livetv" role="tabpanel">
							<div class="row">
								<div class="col-lg-8">
									<div class="alert alert-info">
										<i class="fas fa-info-circle"></i>
										<strong>Informasi Live TV:</strong> Anda dapat menampilkan siaran langsung suasana Masjidil Haram (Makkah) dan Masjid Nabawi (Madinah) 24 jam nonstop pada rotasi layar TV. Anda juga dapat mengganti link YouTube live jika memiliki saluran siaran sendiri.
									</div>

									<div class="card shadow-sm mb-4">
										<div class="card-header bg-dark text-warning font-weight-bold">
											<i class="fas fa-kaaba mr-1"></i> Siaran Langsung Makkah (Masjidil Haram)
										</div>
										<div class="card-body">
											<div class="form-group">
												<label for="live_makkah_url"><strong>URL / Video ID YouTube Live Makkah</strong></label>
												<input type="text" class="form-control" id="live_makkah_url" name="live_makkah_url"
													value="{{ old('live_makkah_url', $setting->live_makkah_url ?? '') }}"
													placeholder="Contoh: https://www.youtube.com/watch?v=live_stream?channel=UCr_yW_8sC_Yg_U9b_wH5Npg">
												<small class="text-muted">Biarkan kosong untuk menggunakan saluran resmi default Saudi Quran TV.</small>
											</div>
										</div>
									</div>

									<div class="card shadow-sm mb-4">
										<div class="card-header bg-dark text-success font-weight-bold">
											<i class="fas fa-mosque mr-1"></i> Siaran Langsung Madinah (Masjid Nabawi)
										</div>
										<div class="card-body">
											<div class="form-group">
												<label for="live_madinah_url"><strong>URL / Video ID YouTube Live Madinah</strong></label>
												<input type="text" class="form-control" id="live_madinah_url" name="live_madinah_url"
													value="{{ old('live_madinah_url', $setting->live_madinah_url ?? '') }}"
													placeholder="Contoh: https://www.youtube.com/watch?v=live_stream?channel=UCaT_20Vp2Zq0FzXp3m4bVrg">
												<small class="text-muted">Biarkan kosong untuk menggunakan saluran resmi default Saudi Sunnah TV.</small>
											</div>
										</div>
									</div>

									<div class="card shadow-sm mb-4">
										<div class="card-header bg-light font-weight-bold">
											<i class="fas fa-sliders-h mr-1"></i> Pengaturan Tampilan Layar Live
										</div>
										<div class="card-body">
											<div class="form-group mb-3">
												<label class="font-weight-bold d-block">Overlay Smart Mosque (Jam, Jadwal Sholat & Running Text)</label>
												<label class="switch">
													<input type="checkbox" name="live_stream_overlay" value="1" {{ ($setting->live_stream_overlay ?? true) ? 'checked' : '' }}>
													<span class="slider round"></span>
												</label>
												<small class="form-text text-muted">Jika aktif, siaran video live akan dihiasi jam digital, jadwal sholat, dan teks berjalan masjid yang elegan.</small>
											</div>

											<div class="form-group mb-0">
												<label class="font-weight-bold d-block">Suara Siaran Live (Audio)</label>
												<label class="switch">
													<input type="checkbox" name="live_stream_audio" value="1" {{ ($setting->live_stream_audio ?? false) ? 'checked' : '' }}>
													<span class="slider round"></span>
												</label>
												<small class="form-text text-muted">Secara default suara dimatikan (Mute) agar suasana masjid tetap hening dan tidak mengganggu pengumuman lokal.</small>
											</div>
										</div>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="card shadow-sm border-left-warning mb-4">
										<div class="card-header bg-warning text-dark font-weight-bold">
											<i class="fas fa-tv mr-1"></i> Cara Menampilkan di TV
										</div>
										<div class="card-body">
											<p class="small text-muted">Agar siaran live ini muncul di layar TV masjid:</p>
											<ol class="small pl-3 text-muted">
												<li>Buka menu <strong>Rotasi Halaman</strong> di sidebar.</li>
												<li>Centang <strong>Live TV Mekah</strong> atau <strong>Live TV Madinah</strong>.</li>
												<li>Klik tombol <strong>Simpan Perubahan</strong>.</li>
											</ol>
											<div class="text-center mt-3">
												<a href="{{ route('rotation.index') }}" class="btn btn-sm btn-outline-primary">
													<i class="fas fa-exchange-alt mr-1"></i> Buka Menu Rotasi
												</a>
											</div>
										</div>
									</div>

									<div class="card shadow-sm border-left-info">
										<div class="card-header bg-info text-white font-weight-bold">
											<i class="fas fa-wifi mr-1"></i> Kebutuhan Internet
										</div>
										<div class="card-body">
											<p class="small text-muted mb-0">
												Siaran live streaming YouTube memerlukan koneksi internet stabil (minimal 2 Mbps). Jika internet masjid sedang terputus, sistem secara otomatis akan melewati (*skip*) siaran live ke slide berikutnya dengan aman.
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB CCTV MIMBAR & TV LUAR --}}
						<div class="tab-pane fade" id="cctvmimbar" role="tabpanel">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-success border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, rgba(30, 90, 58, 0.15), rgba(16, 185, 129, 0.1)); border-left: 5px solid #10b981 !important;">
										<h5 class="font-weight-bold text-success mb-1">
											<i class="fas fa-camera mr-2"></i> Integrasi CCTV Mimbar Masjid ke TV Luar (Serambi)
										</h5>
										<p class="mb-0 text-muted">
											Hubungkan kamera CCTV mimbar yang sudah ada (kabel BNC via DVR lokal atau IP Camera) agar dapat disiarkan langsung ke TV di serambi/halaman masjid saat Sholat Jum'at, Idul Fitri, dan Idul Adha.
										</p>
									</div>
								</div>

								<div class="col-lg-8">
									<div class="card shadow-sm mb-4 border-0">
										<div class="card-header font-weight-bold text-white" style="background: linear-gradient(135deg, #1e5a3a 0%, #0a2e1f 100%);">
											<i class="fas fa-toggle-on text-warning mr-2"></i> Status & Otomatisasi Siaran CCTV
										</div>
										<div class="card-body">
											<div class="form-group mb-4">
												<label class="font-weight-bold d-block text-gray-800">Aktifkan Fitur CCTV Mimbar</label>
												<label class="switch">
													<input type="checkbox" name="cctv_mimbar_enabled" value="1" {{ ($setting->cctv_mimbar_enabled ?? false) ? 'checked' : '' }}>
													<span class="slider round"></span>
												</label>
												<small class="form-text text-muted">Centang untuk mengizinkan sistem menyajikan siaran live kamera mimbar ke TV luar.</small>
											</div>

											<div class="form-group mb-0">
												<label class="font-weight-bold d-block text-gray-800">Otomatis Beralih ke Kamera Mimbar saat Khutbah Jum'at & Sholat Hari Raya</label>
												<label class="switch">
													<input type="checkbox" name="cctv_auto_switch_khutbah" value="1" {{ ($setting->cctv_auto_switch_khutbah ?? true) ? 'checked' : '' }}>
													<span class="slider round"></span>
												</label>
												<small class="form-text text-muted">Jika aktif, TV di luar masjid otomatis berhenti berotasi dan langsung beralih menyiarkan wajah khatib di mimbar begitu waktu khutbah dimulai.</small>
											</div>
										</div>
									</div>

									<div class="card shadow-sm mb-4 border-0">
										<div class="card-header font-weight-bold text-white bg-dark">
											<i class="fas fa-network-wired text-info mr-2"></i> Alamat Stream Kamera Mimbar (DVR / Converter / IP Cam)
										</div>
										<div class="card-body">
											<div class="form-group mb-3">
												<label for="cctv_mimbar_url" class="font-weight-bold text-gray-800">URL Stream Kamera Mimbar</label>
												<input type="text" class="form-control" id="cctv_mimbar_url" name="cctv_mimbar_url"
													value="{{ old('cctv_mimbar_url', $setting->cctv_mimbar_url ?? '') }}"
													placeholder="Contoh: http://192.168.1.100:1984/stream.html?src=mimbar atau rtsp://admin:pass@192.168.1.50:554/ch1">
												<small class="form-text text-muted mt-2">
													<i class="fas fa-info-circle text-primary"></i> <strong>Format yang Didukung:</strong>
													<ul class="pl-3 mt-1 mb-0">
														<li><strong>WebRTC / HLS Lokal (Disarankan - Nol Delay):</strong> <code>http://[IP-Komputer-Server]:1984/stream.html?src=mimbar</code></li>
														<li><strong>RTSP Langsung dari DVR Masjid:</strong> <code>rtsp://admin:password@[IP-DVR]:554/Streaming/Channels/101</code></li>
														<li><strong>YouTube Live / RTMP:</strong> <code>https://www.youtube.com/watch?v=xxxx</code></li>
													</ul>
												</small>
											</div>
										</div>
									</div>
								</div>

								<div class="col-lg-4">
									<div class="card shadow-sm border-left-success mb-4">
										<div class="card-header bg-success text-white font-weight-bold">
											<i class="fas fa-tv mr-2"></i> Cara Pasang di TV Luar
										</div>
										<div class="card-body">
											<p class="small text-muted mb-2">Pada TV serambi/luar masjid (Smart TV atau Android TV Box), buka browser dan ketik alamat:</p>
											<div class="bg-light p-2 rounded border mb-3 text-center">
												<code class="font-weight-bold text-success" style="font-size: 0.95rem;">
													http://[IP-SERVER]:8000/tv-outdoor
												</code>
											</div>
											<p class="small text-muted mb-3">
												TV luar akan memutar rotasi display jadwal sholat biasa pada hari biasa, dan otomatis beralih menampilkan kamera mimbar saat khutbah berlangsung.
											</p>
											<div class="d-flex flex-column gap-2">
												<a href="{{ route('rotator.outdoor') }}" class="btn btn-outline-success btn-sm mb-2" target="_blank">
													<i class="fas fa-external-link-alt mr-1"></i> Preview Layar TV Luar
												</a>
												<a href="{{ route('live-mimbar.embed') }}" class="btn btn-outline-primary btn-sm" target="_blank">
													<i class="fas fa-video mr-1"></i> Preview Siaran Mimbar Saja
												</a>
											</div>
										</div>
									</div>

									<div class="card shadow-sm border-left-warning">
										<div class="card-header bg-warning text-dark font-weight-bold">
											<i class="fas fa-book-open mr-2"></i> Panduan DVR Kabel BNC
										</div>
										<div class="card-body">
											<p class="small text-muted mb-0">
												Untuk kamera analog BNC, cukup colok kabel LAN dari DVR ke router masjid. Mesin DVR otomatis memancarkan video channel kamera mimbar ke jaringan lokal tanpa perlu beli kamera baru. Panduan lengkap tersedia di file <a href="#" class="font-weight-bold">TUTORIAL_CCTV_MIMBAR_TV_LUAR.md</a>.
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB GOOGLE GEMINI AI --}}
						<div class="tab-pane fade" id="ai" role="tabpanel">
							<div class="row">
								<div class="col-lg-7">
									<div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; border: 1px solid rgba(79,70,229,0.2) !important;">
										<div class="card-header py-3" style="background: linear-gradient(135deg, rgba(79,70,229,0.08), rgba(30,90,58,0.05));">
											<h6 class="m-0 font-weight-bold" style="color: #0e3521;">
												<i class="fas fa-key text-warning mr-2"></i> Konfigurasi API Google Gemini
											</h6>
										</div>
										<div class="card-body">
											<div class="form-group">
												<label for="gemini_api_key"><strong>Gemini API Key</strong></label>
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fas fa-lock text-primary"></i></span>
													</div>
													<input type="password" class="form-control font-monospace" id="gemini_api_key" name="gemini_api_key"
														value="{{ old('gemini_api_key', $setting->gemini_api_key ?? '') }}"
														placeholder="AIzaSy...">
													<div class="input-group-append">
														<button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
															<i class="fas fa-eye" id="eyeIcon"></i>
														</button>
													</div>
												</div>
												<small class="form-text text-muted">
													API Key didapatkan gratis dari <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-weight-bold text-primary">Google AI Studio <i class="fas fa-external-link-alt small"></i></a>. Disimpan aman pada database aplikasi.
												</small>
											</div>

											<div class="form-group">
												<label for="gemini_model"><strong>Model AI Gemini</strong></label>
												<select class="form-control" id="gemini_model" name="gemini_model">
													<option value="gemini-1.5-flash" {{ ($setting->gemini_model ?? 'gemini-1.5-flash') == 'gemini-1.5-flash' ? 'selected' : '' }}>
														gemini-1.5-flash (Direkomendasikan - Cepat & Kuota Gratis Melimpah)
													</option>
													<option value="gemini-2.0-flash" {{ ($setting->gemini_model ?? '') == 'gemini-2.0-flash' ? 'selected' : '' }}>
														gemini-2.0-flash (Generasi 2 Terbaru)
													</option>
													<option value="gemini-1.5-pro" {{ ($setting->gemini_model ?? '') == 'gemini-1.5-pro' ? 'selected' : '' }}>
														gemini-1.5-pro (Penalaran Tinggi)
													</option>
												</select>
											</div>

											<div class="d-flex flex-wrap align-items-center mt-4" style="gap: 10px;">
												<button type="button" id="btnTestGemini" class="btn btn-info btn-sm px-3 shadow-sm" style="border-radius: 8px;">
													<i class="fas fa-vial mr-1"></i> Uji Koneksi API Sekarang
												</button>

												<button type="button" id="btnRefreshHikmah" class="btn btn-success btn-sm px-3 shadow-sm" style="border-radius: 8px; background: #1e5a3a; border-color: #1e5a3a;">
													<i class="fas fa-sync-alt mr-1"></i> 🔄 Refresh Hadits Hari Ini dengan AI
												</button>
											</div>

											<!-- Test Result Box -->
											<div id="testResultBox" class="mt-3 d-none">
												<div id="testResultAlert" class="alert mb-0" style="border-radius: 10px;"></div>
											</div>
										</div>
									</div>
								</div>

								<!-- Kolom Kanan: Panduan & Status -->
								<div class="col-lg-5">
									<div class="card shadow-sm border-left-info mb-4">
										<div class="card-header bg-light font-weight-bold" style="color: #0e3521;">
											<i class="fas fa-info-circle text-info mr-2"></i> Cara Mendapatkan API Key Gratis:
										</div>
										<div class="card-body small" style="line-height: 1.8;">
											<ol class="pl-3 mb-2">
												<li>Kunjungi situs resmi <a href="https://aistudio.google.com/app/apikey" target="_blank" class="font-weight-bold">Google AI Studio</a>.</li>
												<li>Login menggunakan akun Google (Gmail).</li>
												<li>Klik tombol biru <strong>"Create API Key"</strong>.</li>
												<li>Salin (*copy*) kunci API yang diawali <code>AIzaSy...</code>.</li>
												<li>Tempelkan (*paste*) pada kolom di samping kiri, lalu klik <strong>Simpan Semua Perubahan</strong>.</li>
											</ol>
											<div class="p-2 rounded bg-light border text-muted">
												<i class="fas fa-shield-alt text-success mr-1"></i> <strong>Aman & Gratis:</strong> Kuota gratis Google AI Studio cukup untuk ribuan generate pengumuman dan mutiara hadits per bulan tanpa biaya.
											</div>
										</div>
									</div>

									<div class="card shadow-sm border-left-success">
										<div class="card-header bg-light font-weight-bold" style="color: #0e3521;">
											<i class="fas fa-tv text-success mr-2"></i> Pratinjau Slide Mutiara Hikmah TV
										</div>
										<div class="card-body small">
											<p class="text-muted mb-3">
												Slide ini otomatis aktif di layar TV masjid (durasi default rotasi), menampilkan hadits tematik harian yang berganti secara mandiri.
											</p>
											<a href="{{ route('hikmah.embed') }}" class="btn btn-outline-success btn-sm btn-block" target="_blank" style="border-radius: 8px;">
												<i class="fas fa-external-link-alt mr-1"></i> Buka Tampilan Slide TV (/hikmah-embed)
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						{{-- TAB AGENDA MALAM JUMAT (SURAT YAASIIN) --}}
						<div class="tab-pane fade" id="yasin" role="tabpanel">
							<div class="row">
								<div class="col-lg-7">
									<div class="card shadow-sm mb-4 border-0">
										<div class="card-header font-weight-bold text-white" style="background: linear-gradient(135deg, #0d4a2b 0%, #062616 100%);">
											<i class="fas fa-book-quran text-warning mr-2"></i> Konfigurasi Agenda Rutin Malam Jum'at
										</div>
										<div class="card-body">
											<div class="form-group mb-4">
												<label class="font-weight-bold d-block text-gray-800">Aktifkan Agenda Malam Jum'at (Surat Yaasiin)</label>
												<label class="switch">
													<input type="checkbox" name="yasin_mode_enabled" value="1" {{ ($setting->yasin_mode_enabled ?? true) ? 'checked' : '' }}>
													<span class="slider round"></span>
												</label>
												<small class="form-text text-muted">
													Jika aktif, setiap Kamis malam mulai jam yang ditentukan hingga waktu Adzan Isya, layar TV masjid otomatis memutar pembacaan Surat Yaasiin 83 ayat secara penuh dengan gulir otomatis (*smooth scroll*). Jika suatu pekan acara ditiadakan karena hal mendesak, cukup nonaktifkan saklar ini.
												</small>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group mb-3">
														<label for="yasin_start_time" class="font-weight-bold text-gray-800">
															<i class="far fa-clock text-success mr-1"></i> Jam Mulai Dimulai (Kamis Malam)
														</label>
														<input type="time" class="form-control" id="yasin_start_time" name="yasin_start_time"
															value="{{ old('yasin_start_time', $setting->yasin_start_time ?? '18:30') }}">
														<small class="form-text text-muted">
															Default: <strong>18:30</strong> (setelah zikir sholat Maghrib selesai).
														</small>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group mb-3">
														<label for="yasin_scroll_speed" class="font-weight-bold text-gray-800">
															<i class="fas fa-gauge-high text-info mr-1"></i> Kecepatan Gulir Teks Arab
														</label>
														@php
															$currentSpeed = old('yasin_scroll_speed', $setting->yasin_scroll_speed ?? 'medium');
														@endphp
														<select class="form-control" id="yasin_scroll_speed" name="yasin_scroll_speed">
															<option value="slow" {{ $currentSpeed == 'slow' ? 'selected' : '' }}>Santai (Perlahan / ~22-25 Menit)</option>
															<option value="medium" {{ $currentSpeed == 'medium' ? 'selected' : '' }}>Normal (Disarankan / ~16-18 Menit)</option>
															<option value="fast" {{ $currentSpeed == 'fast' ? 'selected' : '' }}>Cepat (~10-12 Menit)</option>
														</select>
														<small class="form-text text-muted">
															Kecepatan pergerakan ayat Arab ke atas di layar TV.
														</small>
													</div>
												</div>
											</div>

											<div class="alert alert-info border-0 shadow-sm mb-0" style="background: rgba(13, 74, 43, 0.08); border-left: 4px solid #0d4a2b !important;">
												<h6 class="font-weight-bold text-success mb-1">
													<i class="fas fa-shield-alt mr-1"></i> Otomatisasi & Safety Lock Sholat Isya:
												</h6>
												<p class="small text-muted mb-0">
													Begitu waktu hitung mundur Adzan Isya (atau waktu Adzan Isya) tiba, layar Surat Yaasiin <strong>secara otomatis langsung mengalah (*auto-yield*)</strong> dan beralih ke <strong>Prayer Mode Sholat Isya</strong>. Setelah sholat Isya selesai, TV kembali ke rotasi informasi normal.
												</p>
											</div>
										</div>
									</div>
								</div>

								<div class="col-lg-5">
									<div class="card shadow-sm border-left-success mb-4">
										<div class="card-header bg-success text-white font-weight-bold">
											<i class="fas fa-tv mr-2"></i> Pratinjau Tampilan TV Yaasiin
										</div>
										<div class="card-body">
											<p class="small text-muted mb-3">
												Anda dapat melihat langsung tampilan mushaf digital Surat Yaasiin tanpa harus menunggu hari Kamis malam tiba:
											</p>
											<div class="d-flex flex-column" style="gap: 10px;">
												<a href="{{ route('yasin.embed') }}" class="btn btn-outline-success btn-sm shadow-sm" target="_blank" style="border-radius: 8px;">
													<i class="fas fa-external-link-alt mr-1"></i> Buka Tampilan Layar Penuh (/yasin-embed)
												</a>
												<a href="/prayer-mode/status?debug=1&debug_yasin=1" class="btn btn-outline-secondary btn-sm shadow-sm" target="_blank" style="border-radius: 8px;">
													<i class="fas fa-code mr-1"></i> Cek Status API JSON (/prayer-mode/status?debug_yasin=1)
												</a>
											</div>
										</div>
									</div>

									<div class="card shadow-sm border-left-warning">
										<div class="card-header bg-light font-weight-bold" style="color: #856404;">
											<i class="fas fa-lightbulb text-warning mr-2"></i> Tips Pelaksanaan Jamaah:
										</div>
										<div class="card-body small" style="line-height: 1.8;">
											<ul class="pl-3 mb-0 text-muted">
												<li>Layar menggunakan font mushaf resmi Madinah/Kemenag ukuran besar sehingga jamaah di saf belakang tetap dapat membaca jelas.</li>
												<li>Di pojok kanan bawah terdapat tombol kontrol diskret (*Play/Pause* & Ubah Kecepatan) yang dapat disentuh jika layar TV masjid berupa Touchscreen atau memiliki mouse pointer.</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr class="my-4">

					<div class="text-right">
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
						</button>
						<a href="{{ route('home') }}" class="btn btn-secondary">
							<i class="fas fa-arrow-left mr-2"></i> Kembali
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		// Tampilkan/sembunyikan opsi auto-update
		document.getElementById('auto_update_jadwal').addEventListener('change', function () {
			document.getElementById('autoUpdateOptions').style.display = this.checked ? 'block' : 'none';
		});

		// Menampilkan nama file di input file
		document.querySelectorAll('.custom-file-input').forEach(function (input) {
			input.addEventListener('change', function (e) {
				var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file';
				var nextSibling = e.target.nextElementSibling;
				nextSibling.innerText = fileName;
			});
		});

		// Validasi form sebelum submit
		document.getElementById('settingsForm').addEventListener('submit', function (e) {
			if (document.getElementById('auto_update_jadwal').checked) {
				const city = document.getElementById('auto_update_city').value;
				const country = document.getElementById('auto_update_country').value;
				const time = document.getElementById('auto_update_time').value;

				if (!city || !country || !time) {
					e.preventDefault();
					alert('Semua field auto-update harus diisi jika fitur diaktifkan!');

					// Buka tab auto-update
					$('#autoupdate-tab').tab('show');
				}
			}
		});

		// Simpan posisi tab terakhir
		$(document).ready(function () {
			// Jika ada error di tab auto-update, buka tab tersebut
			@if($errors->hasAny(['auto_update_frequency', 'auto_update_time', 'auto_update_city', 'auto_update_country', 'auto_update_method']))
				$('#autoupdate-tab').tab('show');
			@endif
		});

		// Toggle API Key visibility
		const btnToggleApiKey = document.getElementById('toggleApiKey');
		if (btnToggleApiKey) {
			btnToggleApiKey.addEventListener('click', function () {
				const input = document.getElementById('gemini_api_key');
				const icon = document.getElementById('eyeIcon');
				if (input.type === 'password') {
					input.type = 'text';
					icon.className = 'fas fa-eye-slash';
				} else {
					input.type = 'password';
					icon.className = 'fas fa-eye';
				}
			});
		}

		// Test Gemini Connection
		const btnTestGemini = document.getElementById('btnTestGemini');
		if (btnTestGemini) {
			btnTestGemini.addEventListener('click', function () {
				const box = document.getElementById('testResultBox');
				const alertEl = document.getElementById('testResultAlert');
				btnTestGemini.disabled = true;
				btnTestGemini.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menguji koneksi...';
				box.classList.remove('d-none');
				alertEl.className = 'alert alert-info';
				alertEl.textContent = 'Menghubungkan ke Google Gemini API...';

				fetch("{{ route('ai.test-connection') }}", {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				})
				.then(r => r.json())
				.then(res => {
					btnTestGemini.disabled = false;
					btnTestGemini.innerHTML = '<i class="fas fa-vial mr-1"></i> Uji Koneksi API Sekarang';
					if (res.success) {
						alertEl.className = 'alert alert-success';
						alertEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + res.message;
					} else {
						alertEl.className = 'alert alert-danger';
						alertEl.innerHTML = '<i class="fas fa-times-circle mr-1"></i> ' + res.message;
					}
				})
				.catch(err => {
					btnTestGemini.disabled = false;
					btnTestGemini.innerHTML = '<i class="fas fa-vial mr-1"></i> Uji Koneksi API Sekarang';
					alertEl.className = 'alert alert-danger';
					alertEl.textContent = 'Kesalahan jaringan: ' + err.message;
				});
			});
		}

		// Refresh Hikmah
		const btnRefreshHikmah = document.getElementById('btnRefreshHikmah');
		if (btnRefreshHikmah) {
			btnRefreshHikmah.addEventListener('click', function () {
				const box = document.getElementById('testResultBox');
				const alertEl = document.getElementById('testResultAlert');
				btnRefreshHikmah.disabled = true;
				btnRefreshHikmah.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui Hadits...';
				box.classList.remove('d-none');
				alertEl.className = 'alert alert-info';
				alertEl.textContent = 'Menghasilkan hadits dan hikmah baru hari ini dengan AI...';

				fetch("{{ route('ai.refresh-hikmah') }}", {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				})
				.then(r => r.json())
				.then(res => {
					btnRefreshHikmah.disabled = false;
					btnRefreshHikmah.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> 🔄 Refresh Hadits Hari Ini dengan AI';
					if (res.success) {
						alertEl.className = 'alert alert-success';
						alertEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + res.message + '<br><small class="font-weight-bold">"' + (res.data ? res.data.tema : '') + '"</small>';
					} else {
						alertEl.className = 'alert alert-danger';
						alertEl.textContent = res.message || 'Gagal memperbarui hadits.';
					}
				})
				.catch(err => {
					btnRefreshHikmah.disabled = false;
					btnRefreshHikmah.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> 🔄 Refresh Hadits Hari Ini dengan AI';
					alertEl.className = 'alert alert-danger';
					alertEl.textContent = 'Kesalahan jaringan: ' + err.message;
				});
			});
		}

		// Toggle Semua Accordion Teks Berjalan Halaman (Opsi 3)
		const btnToggleAll = document.getElementById('btnToggleAllRunningPages');
		if (btnToggleAll) {
			let isAllExpanded = false;
			btnToggleAll.addEventListener('click', function () {
				isAllExpanded = !isAllExpanded;
				$('#accordionRunningPages .collapse').collapse(isAllExpanded ? 'show' : 'hide');
				btnToggleAll.innerHTML = isAllExpanded 
					? '<i class="fas fa-compress-arrows-alt mr-1"></i> Tutup Semua Panel'
					: '<i class="fas fa-expand-arrows-alt mr-1"></i> Buka / Tutup Semua Panel';
			});
		}

		// Auto-buka tab sesuai hash URL (misal #prayermode)
		if (window.location.hash) {
			$('#settingsTab a[href="' + window.location.hash + '"]').tab('show');
		}
	</script>
@endpush