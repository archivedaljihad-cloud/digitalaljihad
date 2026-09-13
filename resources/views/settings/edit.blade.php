{{-- resources/views/settings/edit.blade.php --}}
@extends('layouts.admin')

@section('main-content')
	<div class="container-fluid">
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">Pengaturan Aplikasi</h1>
		</div>

		@if (session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
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
							<i class="fas fa-mosque" style="color: #ffd700;"></i> Prayer Mode
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
				</ul>
			</div>
			<div class="card-body">
				<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
					@csrf
					@method('PUT')

					<div class="tab-content" id="settingsTabContent">
						{{-- TAB UMUM --}}
						<div class="tab-pane fade show active" id="general" role="tabpanel">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="nama_aplikasi">Nama Aplikasi <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi"
											value="{{ old('nama_aplikasi', $setting->nama_aplikasi ?? '') }}" required>
									</div>

									<div class="form-group">
										<label for="footer">Footer Text</label>
										<textarea class="form-control" id="footer" name="footer"
											rows="3">{{ old('footer', $setting->footer ?? '') }}</textarea>
										<small class="text-muted">HTML diperbolehkan</small>
									</div>

									<div class="form-group">
										<label for="running_text"><strong>Teks Berjalan (Bisa Banyak Pesan Bergantian)</strong></label>
										<textarea class="form-control" id="running_text" name="running_text"
											rows="7" placeholder="Tuliskan teks berjalan. Tekan ENTER untuk membuat pesan berikutnya (1 baris = 1 pesan bergantian)...">{{ old('running_text', $setting->running_text ?? '') }}</textarea>
										<small class="text-muted d-block mt-1">
											<i class="fas fa-info-circle text-primary"></i> <strong>Model Bergantian (Model A):</strong> Setiap baris kalimat baru (Enter) akan otomatis ditampilkan bergantian satu per satu di layar TV. Anda bisa memasukkan 6 s/d 11 pesan hadits, doa, atau himbauan jamaah.
										</small>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="favicon">Favicon</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="favicon" name="favicon"
												accept=".ico,.png,.jpg,.jpeg,.gif">
											<label class="custom-file-label" for="favicon">Pilih file favicon</label>
										</div>
										@if($setting->favicon)
											<div class="mt-3">
												<p class="mb-1">Favicon Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->favicon) }}" width="64" height="64"
													class="img-thumbnail d-block">
												<small class="text-muted">Rekomendasi: 64x64 px (format .ico atau .png)</small>
											</div>
										@endif
									</div>
								</div>
							</div>

							<hr class="my-4">

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="logo">Logo Aplikasi</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="logo" name="logo"
												accept=".png,.jpg,.jpeg,.gif,.svg">
											<label class="custom-file-label" for="logo">Pilih file logo</label>
										</div>
										@if($setting->logo)
											<div class="mt-3">
												<p class="mb-1">Logo Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->logo) }}" class="img-thumbnail"
													style="max-height: 150px; width: auto;">
												<small class="text-muted">Rekomendasi: maksimal 300x150 px (format .png dengan
													background transparan)</small>
											</div>
										@endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="background">Background Sidebar</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="background" name="background"
												accept=".jpg,.jpeg,.png,.gif">
											<label class="custom-file-label" for="background">Pilih file background</label>
										</div>
										@if($setting->background)
											<div class="mt-3">
												<p class="mb-1">Background Saat Ini:</p>
												<img src="{{ asset('storage/' . $setting->background) }}" class="img-thumbnail"
													style="max-height: 150px; width: 100%; object-fit: cover;">
												<small class="text-muted">Rekomendasi: 1920x1080 px (format .jpg atau
													.png)</small>
											</div>
										@endif
									</div>
								</div>
							</div>

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

						{{-- TAB PRAYER MODE --}}
						<div class="tab-pane fade" id="prayermode" role="tabpanel">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										<i class="fas fa-info-circle"></i>
										<strong>Pengaturan Prayer Mode:</strong> Atur durasi hitung mundur, pesan, dan tampilan layar saat masuk waktu sholat.
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<div class="custom-control custom-switch mt-2 mb-4">
											<input type="hidden" name="prayer_mode_enabled" value="0">
											<input type="checkbox" class="custom-control-input" id="prayer_mode_enabled"
												name="prayer_mode_enabled" value="1" {{ old('prayer_mode_enabled', $setting->prayer_mode_enabled ?? 1) ? 'checked' : '' }}>
											<label class="custom-control-label" for="prayer_mode_enabled">
												<strong>Aktifkan Prayer Mode</strong>
											</label>
										</div>
									</div>

									<h6 class="font-weight-bold text-success mb-3 border-bottom pb-2">Pengaturan Waktu & Durasi</h6>
									
									<div class="form-group">
										<label>Durasi Sholat Keseluruhan (Menit)</label>
										<input type="number" name="prayer_mode_duration" class="form-control" value="{{ old('prayer_mode_duration', $setting->prayer_mode_duration ?? 10) }}" min="1">
									</div>

									<div class="form-group">
										<label>Durasi Countdown Sebelum Adzan (Menit)</label>
										<input type="number" name="countdown_adzan_duration" class="form-control" value="{{ old('countdown_adzan_duration', $setting->countdown_adzan_duration ?? 5) }}" min="1">
									</div>

									<div class="form-group">
										<label>Durasi Iqamah (Menit)</label>
										<input type="number" name="iqamah_duration" class="form-control" value="{{ old('iqamah_duration', $setting->iqamah_duration ?? 10) }}" min="1">
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
	</script>
@endpush