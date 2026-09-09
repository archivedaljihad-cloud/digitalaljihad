<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slide Informasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/display-theme.css') }}">
    @include('partials.display-theme')
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            text-align: center;
            margin-top: 0;
            margin-bottom: 8px;
            flex-shrink: 0;
            position: relative;
            z-index: 50;
        }

        .header h1 {
            font-family: 'Masking Renta', sans-serif !important;
            font-size: 3.2rem !important;
            line-height: 1.1 !important;
            letter-spacing: 5px !important;
            text-transform: uppercase !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
            color: #0b4f26 !important;
            text-shadow:
                2px 2px 0 #ffffff,
                -2px 2px 0 #ffffff,
                2px -2px 0 #ffffff,
                -2px -2px 0 #ffffff,
                0 0 12px #ffd700,
                0 0 25px #ffd700,
                0 0 40px rgba(255, 170, 0, 0.06) !important;
            filter: none !important;
            margin: 0 0 -15px 0 !important;
            padding: 0 !important;
        }

        .header h3.sub-header {
            font-family: 'Poppins', sans-serif !important;
            font-size: 1.25rem !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            letter-spacing: 4px !important;
            color: #ffffff !important;
            text-transform: uppercase !important;
            opacity: 0.95 !important;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
            margin-top: 10px !important;
            margin-bottom: 6px !important;
            padding: 0 !important;
        }

        .datetime {
            font-size: 1.55rem;
            margin-top: 4px;
            background: rgba(3, 20, 15, 0.65);
            display: inline-block;
            padding: 5px 28px;
            border-radius: 35px;
            font-weight: 600;
            letter-spacing: 1px;
            border: 1.5px solid rgba(255, 215, 0, 0.5);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Container Slide Utama */
        .slide-stage {
            flex: 1;
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide-layout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 10px 35px;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
        }

        /* --- KOLOM KIRI: 3D TILE FLIP / MOSAIC GRID BOARD (EFEK KOTAK-KOTAK BERATURAN) --- */
        .slide-left-col {
            flex: 1.5;
            height: 100%;
            max-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-width: 0;
        }

        .mosaic-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            max-height: 66vh;
        }

        .mosaic-backdrop {
            position: absolute;
            inset: -14px;
            background-size: cover;
            background-position: center;
            filter: blur(28px);
            opacity: 0.45;
            border-radius: 24px;
            transform: scale(1.03);
            z-index: 1;
            pointer-events: none;
            transition: background-image 0.8s ease, opacity 0.8s ease;
        }

        .mosaic-board {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 100%;
            height: 65vh;
            border-radius: 20px;
            background: #020e0a;
            border: 2px solid rgba(255, 215, 0, 0.55);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85), 0 0 30px rgba(255, 215, 0, 0.2);
            overflow: hidden;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            grid-template-rows: repeat(6, 1fr);
            gap: 2px;
            padding: 2px;
            box-sizing: border-box;
            background-color: rgba(255, 215, 0, 0.18); /* Garis batas grid kotak bernuansa emas */
            perspective: 1400px;
        }

        .mosaic-tile {
            position: relative;
            width: 100%;
            height: 100%;
            perspective: 800px;
            transform-style: preserve-3d;
        }

        .tile-flipper {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transform-origin: center center;
            will-change: transform;
        }

        .tile-flipper.animate-flip {
            transition: transform 0.65s cubic-bezier(0.35, 0.0, 0.15, 1.0);
            transform: rotateY(-180deg);
        }

        .tile-face {
            position: absolute;
            inset: 0;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            overflow: hidden;
            background: #020e0a;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.7);
        }

        .tile-face.front {
            transform: rotateY(0deg);
        }

        .tile-face.back {
            transform: rotateY(180deg);
        }

        .tile-slice {
            position: absolute;
            width: 800%;  /* 8 kolom = 800% */
            height: 600%; /* 6 baris = 600% */
            pointer-events: none;
        }

        .tile-slice img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
        }

        .tile-shadow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.15) 50%, rgba(0,0,0,0.9) 100%);
            opacity: 0;
            transition: opacity 0.65s cubic-bezier(0.35, 0.0, 0.15, 1.0);
            transition-delay: inherit;
        }

        .tile-flipper.animate-flip .tile-face.front .tile-shadow {
            opacity: 0.75;
        }

        .tile-flipper.animate-flip .tile-face.back .tile-shadow {
            opacity: 0;
        }

        /* --- KOLOM KANAN: PANEL GLASSMORPHISM ELEGAN --- */
        .slide-right-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            max-height: 100%;
            min-width: 0;
        }

        .slide-card {
            background: rgba(4, 25, 18, 0.75);
            border: 1.5px solid rgba(255, 215, 0, 0.45);
            border-radius: 24px;
            padding: 35px 36px;
            text-align: center;
            align-items: center;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.65), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            max-height: 65vh;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .slide-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.22), rgba(0, 230, 118, 0.22));
            border: 1px solid rgba(255, 215, 0, 0.5);
            color: #ffd700;
            padding: 7px 22px;
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 18px;
            align-self: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
        }

        .slide-badge i {
            color: #ffd700;
            font-size: 0.95rem;
        }

        .card-text-wrapper {
            width: 100%;
            transition: transform 0.45s cubic-bezier(0.35, 0, 0.2, 1), opacity 0.4s ease, filter 0.4s ease;
            transform-origin: center top;
            will-change: transform, opacity;
        }

        .card-text-wrapper.flip-out {
            transform: rotateX(35deg) translateY(-12px);
            opacity: 0;
            filter: blur(2px);
        }

        .card-text-wrapper.flip-in {
            transform: rotateX(0deg) translateY(0);
            opacity: 1;
            filter: blur(0);
        }

        .slide-card .judul {
            font-family: 'Poppins', sans-serif;
            font-size: 2.15rem;
            font-weight: 700;
            line-height: 1.3;
            color: #ffffff;
            margin-bottom: 16px;
            text-align: center;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.85);
            border-bottom: 2px solid rgba(255, 215, 0, 0.3);
            padding-bottom: 14px;
            width: 100%;
        }

        .slide-card .deskripsi {
            font-size: 1.22rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.92);
            white-space: pre-line;
            text-align: center;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
            overflow-y: auto;
            max-height: 30vh;
            padding: 0 10px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Custom Scrollbar halus jika teks deskripsi sangat panjang */
        .slide-card .deskripsi::-webkit-scrollbar {
            width: 5px;
        }
        .slide-card .deskripsi::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.4);
            border-radius: 10px;
        }

        .slide-indicators {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
        }

        .indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.25);
            transition: all 0.4s cubic-bezier(0.35, 0, 0.2, 1);
        }

        .indicator-dot.active {
            width: 32px;
            background: linear-gradient(90deg, #ffd700, #00e676);
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        }

        .empty {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #d1d5db;
        }
    </style>
</head>

<body>
    <div class="display-background"></div>
    <div class="display-overlay"></div>
    @include('partials.medallion-header')

    <div class="display-content" style="height: 100vh; width: 100vw; position: relative; display: flex; flex-direction: column; justify-content: space-between; padding: 10px 25px 85px 25px; box-sizing: border-box; z-index: 5;">

        <!-- Header Standar Bersama (Pixel-Locked) -->
        <div class="header">
            <h1>{{ $settings['nama_aplikasi'] ?? "MASJID JAMI' AL JIHAD" }}</h1>
            <h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
            <div class="datetime" id="datetime"></div>
        </div>

        <!-- Stage Konten Slide Informasi (Efek 3D Tile Flip / Mosaic Grid) -->
        <div class="slide-stage" id="slideStage">
            @if($slides->count())
            @php
                $firstSlide = $slides[0];
                $firstImg = $firstSlide->gambar ? $firstSlide->gambar_url : asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp');
                $secondImg = (isset($slides[1]) && $slides[1]->gambar) ? $slides[1]->gambar_url : $firstImg;
            @endphp
            <div class="slide-layout">
                
                {{-- Kolom Kiri: 3D Tile Flip / Mosaic Grid Board (48 Kotak: 8 Kolom x 6 Baris) --}}
                <div class="slide-left-col">
                    <div class="mosaic-wrapper">
                        <div class="mosaic-backdrop" id="mosaicBackdrop" style="background-image: url('{{ $firstImg }}');"></div>
                        
                        <div class="mosaic-board" id="mosaicBoard">
                            @for($r = 0; $r < 6; $r++)
                                @for($c = 0; $c < 8; $c++)
                                <div class="mosaic-tile" data-col="{{ $c }}" data-row="{{ $r }}">
                                    <div class="tile-flipper">
                                        <div class="tile-face front">
                                            <div class="tile-slice" style="left: -{{ $c * 100 }}%; top: -{{ $r * 100 }}%;">
                                                <img src="{{ $firstImg }}" alt="{{ $firstSlide->judul }}" onerror="this.onerror=null; this.src='{{ asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp') }}';">
                                            </div>
                                            <div class="tile-shadow"></div>
                                        </div>
                                        <div class="tile-face back">
                                            <div class="tile-slice" style="left: -{{ $c * 100 }}%; top: -{{ $r * 100 }}%;">
                                                <img src="{{ $secondImg }}" alt="" onerror="this.onerror=null; this.src='{{ asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp') }}';">
                                            </div>
                                            <div class="tile-shadow"></div>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Kartu Informasi Elegan (Glassmorphism) dengan Transisi Sinkron --}}
                <div class="slide-right-col">
                    <div class="slide-card">
                        <div class="slide-badge">
                            <i class="fas fa-bullhorn"></i>
                            <span>Informasi Kegiatan</span>
                        </div>
                        <div class="card-text-wrapper" id="cardText">
                            <div class="judul" id="slideJudul">
                                {{ $firstSlide->judul }}
                            </div>
                            <div class="deskripsi" id="slideDeskripsi">
                                {{ $firstSlide->deskripsi ?? '' }}
                            </div>
                        </div>
                        @if($slides->count() > 1)
                        <div class="slide-indicators" id="slideIndicators">
                            @foreach($slides as $idx => $s)
                            <span class="indicator-dot {{ $idx === 0 ? 'active' : '' }}" data-index="{{ $idx }}"></span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- JSON Data Slide untuk Controller 3D Tile Flip / Mosaic Grid -->
            <script id="slides-data" type="application/json">
                {!! json_encode($slides->map(function($s) {
                    return [
                        'id' => $s->id,
                        'judul' => $s->judul,
                        'deskripsi' => $s->deskripsi,
                        'gambar_url' => $s->gambar ? $s->gambar_url : asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp'),
                        'durasi' => max(3, (int)$s->durasi)
                    ];
                })) !!}
            </script>
            @else
            <div class="empty">
                Belum ada Slide Informasi yang aktif.
            </div>
            @endif
        </div>

        <!-- Memanggil Komponen Running Text dan Footer Secara Rapi di Bawah -->
        @include('partials.bottom-section')

    </div>

    <!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK TRIVISION BLINDS SMOOTH) -->
    <script>
        const backgroundImages = [
            "{{ asset('image/display/background/BG1.png') }}",
            "{{ asset('image/display/background/BG2.png') }}",
            "{{ asset('image/display/background/BG3.png') }}",
            "{{ asset('image/display/background/BG4.png') }}",
            "{{ asset('image/display/background/BG5.png') }}",
            "{{ asset('image/display/background/BG6.png') }}",
            "{{ asset('image/display/background/BG7.png') }}"
        ];

        let currentBgIndex = 0;
        const bgContainer = document.querySelector('.display-background');

        const numBlinds = 12;
        let blinds = [];

        if (backgroundImages.length > 0 && bgContainer) {
            bgContainer.style.setProperty('overflow', 'hidden', 'important');
            bgContainer.style.setProperty('position', 'absolute', 'important');
            bgContainer.style.setProperty('z-index', '-1', 'important');
            bgContainer.style.setProperty('perspective', '1600px', 'important');
            bgContainer.style.setProperty('display', 'flex', 'important');

            for (let i = 0; i < numBlinds; i++) {
                const blind = document.createElement('div');
                blind.style.flex = '1';
                blind.style.height = '100%';
                blind.style.position = 'relative';
                blind.style.transformStyle = 'preserve-3d';
                blind.style.transition = 'transform 1.05s cubic-bezier(0.35, 0, 0.25, 1)';
                blind.style.transitionDelay = `${i * 0.075}s`;

                const bgPosX = numBlinds === 1 ? 0 : (i / (numBlinds - 1)) * 100;

                const createFace = (isFront) => {
                    const face = document.createElement('div');
                    face.style.position = 'absolute';
                    face.style.width = '100%';
                    face.style.height = '100%';
                    face.style.backfaceVisibility = 'hidden';
                    face.style.backgroundSize = '100vw 100vh';
                    face.style.backgroundPosition = `${bgPosX}% center`;
                    face.style.backgroundRepeat = 'no-repeat';
                    face.style.boxShadow = 'inset 0 0 10px rgba(0, 0, 0, 0.35)';
                    if (!isFront) {
                        face.style.transform = 'rotateY(180deg)';
                    }
                    return face;
                };

                const front = createFace(true);
                const back = createFace(false);

                front.style.backgroundImage = `url('${backgroundImages[0]}')`;

                blind.appendChild(front);
                blind.appendChild(back);
                bgContainer.appendChild(blind);

                blinds.push({ el: blind, front: front, back: back, isFlipped: false });
            }

            function changeBackground() {
                currentBgIndex = (currentBgIndex + 1) % backgroundImages.length;
                const nextImgUrl = `url('${backgroundImages[currentBgIndex]}')`;

                blinds.forEach((blind) => {
                    if (!blind.isFlipped) {
                        blind.back.style.backgroundImage = nextImgUrl;
                        blind.el.style.transform = 'rotateY(180deg)';
                    } else {
                        blind.front.style.backgroundImage = nextImgUrl;
                        blind.el.style.transform = 'rotateY(0deg)';
                    }
                    blind.isFlipped = !blind.isFlipped;
                });
            }

            setInterval(changeBackground, 10000);
        }
    </script>

    <!-- SCRIPT UNTUK JAM & ROTASI KONTEN SLIDE INFORMASI (EFEK 3D TILE FLIP / MOSAIC GRID) -->
    <script>
        // Jam & Tanggal Standar Masjid
        function updateDateTime() {
            const now = new Date();
            const dt = document.getElementById('datetime');
            if (dt) {
                dt.innerHTML = typeof getStandardMasjidDateTime === 'function'
                    ? getStandardMasjidDateTime(now, true)
                    : now.toLocaleString('id-ID');
            }
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Kontroller 3D Tile Flip / Mosaic Grid Wave untuk Pergantian Gambar & Konten Slide
        document.addEventListener('DOMContentLoaded', function () {
            const dataElement = document.getElementById('slides-data');
            if (!dataElement) return;

            let slides = [];
            try {
                slides = JSON.parse(dataElement.textContent || '[]');
            } catch (e) {
                console.error("Gagal membaca data slide", e);
            }

            if (!slides || slides.length <= 1) return;

            const STAGGER_STEP = 0.04; // 40ms jeda per gelombang diagonal kotak
            let currentIdx = 0;
            let isTransitioning = false;
            let slideTimer = null;

            const board = document.getElementById('mosaicBoard');
            const backdrop = document.getElementById('mosaicBackdrop');
            const cardText = document.getElementById('cardText');
            const slideJudul = document.getElementById('slideJudul');
            const slideDeskripsi = document.getElementById('slideDeskripsi');
            const indicatorDots = document.querySelectorAll('.indicator-dot');

            // Kumpulkan referensi 48 kotak kecil (tiles)
            const tileElements = board.querySelectorAll('.mosaic-tile');
            const tiles = [];
            let maxDiagonal = 0;

            tileElements.forEach(tile => {
                const c = parseInt(tile.getAttribute('data-col'), 10) || 0;
                const r = parseInt(tile.getAttribute('data-row'), 10) || 0;
                const diagonal = c + r;
                if (diagonal > maxDiagonal) maxDiagonal = diagonal;

                const flipper = tile.querySelector('.tile-flipper');
                const frontFace = tile.querySelector('.tile-face.front');
                const backFace = tile.querySelector('.tile-face.back');

                tiles.push({
                    c, r,
                    diagonal,
                    flipper,
                    frontImg: frontFace.querySelector('img'),
                    backImg: backFace.querySelector('img'),
                    frontShadow: frontFace.querySelector('.tile-shadow'),
                    backShadow: backFace.querySelector('.tile-shadow')
                });
            });

            function flipToNextSlide() {
                if (isTransitioning) return;
                isTransitioning = true;

                const nextIdx = (currentIdx + 1) % slides.length;
                const nextSlide = slides[nextIdx];

                // 1. Siapkan gambar berikutnya pada sisi belakang setiap kotak
                tiles.forEach(t => {
                    t.backImg.src = nextSlide.gambar_url;
                    t.backShadow.style.opacity = '0.75';
                    t.frontShadow.style.opacity = '0';
                });

                // 2. Putar balik kotak-kotak secara bergelombang teratur (diagonal wave cascade)
                tiles.forEach(t => {
                    t.flipper.style.transitionDelay = `${t.diagonal * STAGGER_STEP}s`;
                    t.flipper.classList.add('animate-flip');
                });

                // 3. Sinkronkan kilauan ambient backdrop di belakang frame
                setTimeout(() => {
                    if (backdrop) {
                        backdrop.style.backgroundImage = `url('${nextSlide.gambar_url}')`;
                    }
                }, 250);

                // 4. Sinkronkan teks kartu informasi kanan saat gelombang kotak mencapai tengah
                setTimeout(() => {
                    if (cardText) {
                        cardText.classList.remove('flip-in');
                        cardText.classList.add('flip-out');

                        setTimeout(() => {
                            if (slideJudul) slideJudul.textContent = nextSlide.judul;
                            if (slideDeskripsi) slideDeskripsi.textContent = nextSlide.deskripsi || '';
                            cardText.classList.remove('flip-out');
                            cardText.classList.add('flip-in');
                        }, 220);
                    }
                }, 220);

                // Update titik indikator slide
                indicatorDots.forEach((dot, idx) => {
                    dot.classList.toggle('active', idx === nextIdx);
                });

                // 5. Reset instan & mulus setelah seluruh 48 kotak mendarat rata (0 flicker)
                const totalWaveDuration = maxDiagonal * STAGGER_STEP + 0.65 + 0.08;
                setTimeout(() => {
                    tiles.forEach(t => {
                        t.flipper.style.transition = 'none';
                        t.flipper.style.transitionDelay = '0s';
                        t.flipper.classList.remove('animate-flip');
                        t.frontImg.src = nextSlide.gambar_url;
                        void t.flipper.offsetWidth; // Force reflow
                        t.flipper.style.transition = '';
                    });

                    currentIdx = nextIdx;
                    isTransitioning = false;
                    scheduleNextSlide();
                }, totalWaveDuration * 1000);
            }

            function scheduleNextSlide() {
                if (slideTimer) clearTimeout(slideTimer);
                const currentSlide = slides[currentIdx];
                const duration = (parseInt(currentSlide.durasi, 10) || 6) * 1000;
                slideTimer = setTimeout(flipToNextSlide, duration);
            }

            scheduleNextSlide();
        });
    </script>

</body>

</html>