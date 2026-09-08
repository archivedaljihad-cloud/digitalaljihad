<!-- resources/views/prayer-mode.blade.php -->[cite: 1]
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mode Sholat</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;

            /* TEMA DINAMIS */
            @if($theme == 'dark')
                background: #000000;
                color: #ffffff;
            @elseif($theme == 'light')
                background: #f4f6f9;
                color: #333333;
            @else
                /* default / hijau */
                background: #052614;
                color: #ffffff;
            @endif
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* BACKGROUND UTAMA */
        @keyframes emeraldGlow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* BACKGROUND UTAMA DENGAN EFEK GERAKAN HALUS */
        .background {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(120deg, #01160b, #042b17, #0b4f2c, #032212, #01160b);
            background-size: 300% 300%;
            animation: emeraldGlow 22s ease infinite;
        }

        /* LAYER 1: PATTERN ISLAMIC DI BELAKANG */
        .background::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("storage/background/islamic.png") }}');
            background-repeat: repeat;
            background-position: center;
            background-size: 280px;
            opacity: 0.18;
            pointer-events: none;
            z-index: 1;
        }

        /* LAYER 2: GAMBAR KABAH DI TENGAH DENGAN OVERLAY HALUS */
        .background::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("img/Kaabah.png") }}');
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            opacity: 0.28;
            mix-blend-mode: luminosity;
            pointer-events: none;
            z-index: 2;
        }

        /* LAYER 3: OVERLAY GRADIENT AGAR TEKS TETAP SANGAT JELAS */
        .overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: radial-gradient(circle at center, rgba(1, 22, 11, 0.45) 0%, rgba(1, 22, 11, 0.85) 100%);
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1600px;
            padding: 40px;
            text-align: center;
        }

        /* TULISAN ARAB */
        .arabic {
            font-family: 'Amiri', serif;
            font-size: 70px;
            line-height: 1.4;
            color: #FFD54F;
            margin-bottom: 15px;
            transform: translateY(20px);
            text-shadow:
                0 0 10px rgba(255, 213, 79, .3),
                0 0 30px rgba(255, 213, 79, .2);
        }

        /* TULISAN WARNA DINAMIS */
        .title {
            font-size: 50px;
            font-weight: 600;
            @if($theme == 'light')
                color: #1f2937;
                text-shadow: none;
            @else
                color: #ffffff;
            @endif
            letter-spacing: 2px;
            margin-top: 30px;
            margin-bottom: 0px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 40px;
            @if($theme == 'light')
                color: #4b5563;
            @else
                color: #dddddd;
            @endif
            margin-bottom: 0px;
        }

        .countdown-box {
            display: inline-block;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .countdown-label {
            font-size: 26px;
            font-weight: 600;
            @if($theme == 'light')
                color: #374151;
            @else
                color: #ffffff;
            @endif
            margin-bottom: 10px;
        }

        .countdown {
            margin-top: 0;
            font-size: 96px;
            font-weight: 700;
            color: #FFD54F;
            line-height: 1;
            text-shadow:
                0 0 10px rgba(255, 213, 79, .3),
                0 0 25px rgba(255, 213, 79, .2);
        }

        .message {
            margin-top: 20px;
            font-size: 38px;
            font-weight: bold;
            line-height: 1.4;
            @if($theme == 'light')
                color: #1f2937;
            @else
                color: #ffffff;
            @endif
            padding: 0 40px;
        }

        .hadith {
            margin-top: 55px;
            font-size: 26px;
            @if($theme == 'light')
                color: #4b5563;
            @else
                color: #dddddd;
            @endif
            font-style: italic;
            line-height: 1.4;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer {
            margin-top: 70px;
            font-size: 20px;
            color: #ffd700;
        }

        @media (max-width:1200px) {
            .arabic {
                font-size: 72px;
            }

            .title {
                font-size: 42px;
            }

            .countdown {
                font-size: 56px;
            }

            .message {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container">
        <div class="arabic">
            ﴿ وَأَقِيمُوا الصَّلَاةَ ﴾
        </div>

        <div class="title">
            @if($phase == 'countdown')
                MENJELANG WAKTU SHOLAT
            @elseif($phase == 'adzan')
                WAKTU ADZAN
            @elseif($phase == 'iqamah')
                MENUNGGU IQAMAH
            @elseif($phase == 'prayer')
                MODE SHOLAT
            @else
                MODE SHOLAT
            @endif
        </div>

        <div class="subtitle">
            @if($currentPrayer)
                {{ strtoupper(is_object($currentPrayer) ? $currentPrayer->nama_sholat : $currentPrayer) }}
            @else
                Menunggu Waktu Sholat
            @endif
        </div>

        <div class="countdown-box" @if($phase == 'prayer') style="display:none;" @endif>
            <div class="countdown-label">
                @if($phase == 'countdown')
                    Menuju Adzan
                @elseif($phase == 'adzan')
                    Durasi Adzan
                @elseif($phase == 'iqamah')
                    Menuju Iqamah
                @else
                    Sisa Waktu
                @endif
            </div>
            <div class="countdown" id="countdown" data-seconds="{{ $remainingSeconds }}">
                00:00
            </div>
        </div>

        <!-- PESAN DINAMIS DARI DATABASE -->
        <div class="message">
            @php
                $displayMessage = '';

                if ($phase == 'countdown') {
                    $displayMessage = $setting->Countdown_Adzan ?? 'Bersiap Masuk Waktu Sholat';
                } elseif ($phase == 'adzan') {
                    $displayMessage = $setting->Berkumandang_Adzan ?? 'Waktu Adzan Telah Tiba';
                } elseif ($phase == 'iqamah') {
                    $displayMessage = $setting->Hitung_Mundur_Iqamah ?? 'Menuju Waktu Iqamah';
                } elseif ($phase == 'prayer') {
                    $displayMessage = $setting->Sholat_Berlangsung ?? 'Luruskan & Rapatkan Shaf. Matikan Alat Komunikasi.';
                } else {
                    $displayMessage = $setting->Mode_Sholat ?? 'Harap Tenang';
                }
            @endphp
            {!! nl2br(e($displayMessage)) !!}
        </div>

        <div class="hadith">
            Rasulullah ﷺ bersabda:
            <br><br>
            <strong>
                "Shalat berjamaah lebih utama daripada
                shalat sendirian dengan dua puluh tujuh derajat."
            </strong>
            <br>
            <small>
                (HR. Bukhari dan Muslim)
            </small>
        </div>

        <div class="footer">
            {{ $setting->footer ?? '' }}
        </div>

        <script>
            let remaining =
                parseInt(
                    document
                        .getElementById('countdown')
                        .dataset
                        .seconds
                );

            let hasPlayedTarhim = false; // Penanda agar audio tarhim/adzan hanya diputar sekali

            function formatTime(seconds) {
                if (seconds < 0) {
                    seconds = 0;
                }
                const minutes =
                    Math.floor(seconds / 60);
                const secs =
                    seconds % 60;
                return (
                    String(minutes).padStart(2, '0')
                    + ':'
                    +
                    String(secs).padStart(2, '0')
                );
            }

            function updateCountdown() {
                const el =
                    document.getElementById('countdown');
                if (!el) {
                    return;
                }
                el.innerHTML =
                    formatTime(remaining);

                // AUDIO TARHIM BERBUNYI SAAT SISA WAKTU <= 300 DETIK (5 MENIT)
                @if($phase == 'countdown')
                    if (remaining <= {{ $setting->tarhim_trigger_seconds ?? 300 }} && !hasPlayedTarhim) {
                        const tarhimAudio = document.getElementById('audioTarhim');
                        if (tarhimAudio) {
                            tarhimAudio.play().catch(function(error) {
                                console.log("Audio tarhim diblokir browser:", error);
                            });
                        }
                        hasPlayedTarhim = true;
                    }
                @endif

                // AUDIO ADZAN BERBUNYI PAS MASUK WAKTU ADZAN
                @if($phase == 'adzan')
                    const adzanAudio = document.getElementById('audioAdzan');
                    if (adzanAudio && !hasPlayedTarhim) {
                        adzanAudio.play().catch(function(error) {
                            console.log("Audio adzan diblokir browser:", error);
                        });
                        hasPlayedTarhim = true;
                    }
                @endif

                if (remaining > 0) {
                    remaining--;
                } else {
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            }

            @if($phase != 'prayer')
                updateCountdown();
                setInterval(updateCountdown, 1000);
            @endif

            /*
            |--------------------------------------------------------------------------
            | Cek status Mode Sholat
            |--------------------------------------------------------------------------
            */
            setInterval(function () {
                fetch('/prayer-mode/status')
                    .then(response => response.json())
                    .then(function (data) {
                        if (!data.active) {
                            if (window !== window.parent) {
                                // Biarkan parent (rotator) yang mengurus peralihan halaman
                            } else {
                                window.location.href = "/";
                            }
                        } else if (data.phase !== '{{ $phase }}') {
                            window.location.reload();
                        }

                        if (data.theme !== '{{ $theme }}' || data.displayMessage !== @json($displayMessage) || data.bgOpacity !== {{ $bgOpacity }}) {
                            window.location.reload();
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }, 5000);
        </script>
    </div>

    <!-- ELEMEN AUDIO TERSEMBUNYI (DIKONTROL JAVASCRIPT) -->
    @php
        $namaSholatRaw = $currentPrayer ? (is_object($currentPrayer) ? ($currentPrayer->nama_sholat ?? '') : $currentPrayer) : '';
        $isSubuh = strtolower(trim($namaSholatRaw)) === 'subuh';

        // Tentukan file audio tarhim:
        // Jika sholat Subuh: prioritaskan tarhim_audio_subuh, fallback ke tarhim_audio, atau default Subuh.mp3/tarhim2.mp3
        // Jika sholat selain Subuh: prioritaskan tarhim_audio_reguler, fallback ke tarhim_audio, atau default tarhim2.mp3
        if ($isSubuh) {
            if (!empty($setting->tarhim_audio_subuh)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio_subuh);
            } elseif (!empty($setting->tarhim_audio)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio);
            } elseif (file_exists(public_path('audio/Subuh.mp3'))) {
                $tarhimSource = asset('audio/Subuh.mp3');
            } else {
                $tarhimSource = asset('audio/tarhim2.mp3');
            }
        } else {
            if (!empty($setting->tarhim_audio_reguler)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio_reguler);
            } elseif (!empty($setting->tarhim_audio)) {
                $tarhimSource = asset('storage/' . $setting->tarhim_audio);
            } else {
                $tarhimSource = asset('audio/tarhim2.mp3');
            }
        }
    @endphp
    <audio id="audioTarhim">
        <source src="{{ $tarhimSource }}" type="audio/mpeg">
    </audio>
    <audio id="audioAdzan">
        @php
            $namaSholatClean = ucwords(strtolower(trim($namaSholatRaw)));
            $audioFile = 'adzan.mp3?v=2';
            if (in_array($namaSholatClean, ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'])) {
                $audioFile = $namaSholatClean . '.mp3';
            }
        @endphp
        <source src="{{ asset('audio/' . $audioFile) }}" type="audio/mpeg">
    </audio>
</body>

</html>