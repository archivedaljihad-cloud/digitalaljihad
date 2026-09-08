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
            perspective: 1400px; /* Perspective untuk 3D Billboard Flip */
        }

        /* --- OPSI A: SPLIT 2-COLUMN LANDSCAPE LAYOUT DENGAN 3D BILLBOARD FLIP TRANSITION --- */
        .slide {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 10px 35px 10px 35px;
            box-sizing: border-box;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            backface-visibility: hidden;
            transform: rotateY(90deg) scale(0.9);
            transition: transform 0.9s cubic-bezier(0.3, 1.2, 0.4, 1), opacity 0.7s ease-in-out, visibility 0.9s;
        }

        .slide.active {
            opacity: 1;
            visibility: visible;
            transform: rotateY(0deg) scale(1);
            pointer-events: auto;
            z-index: 2;
        }

        .slide.prev-flip {
            opacity: 0;
            visibility: hidden;
            transform: rotateY(-90deg) scale(0.9);
            transition: transform 0.9s cubic-bezier(0.3, 1.2, 0.4, 1), opacity 0.7s ease-in-out, visibility 0.9s;
            z-index: 1;
        }

        /* Kolom Kiri: Poster / Foto Gambar Utama (Diperbesar) */
        .slide-left-col {
            flex: 1.6;
            height: 100%;
            max-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-width: 0;
        }

        .slide-poster-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            max-height: 66vh;
        }

        .slide-poster-backdrop {
            position: absolute;
            inset: -12px;
            background-size: cover;
            background-position: center;
            filter: blur(28px);
            opacity: 0.45;
            border-radius: 20px;
            transform: scale(1.03);
            z-index: 1;
            pointer-events: none;
        }

        .slide-left-col img {
            position: relative;
            z-index: 2;
            max-width: 100%;
            max-height: 65vh;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 18px;
            border: 2px solid rgba(255, 215, 0, 0.5);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.85), 0 0 30px rgba(255, 215, 0, 0.2);
            transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Kolom Kanan: Panel Glassmorphism Elegan Rata Tengah (Center All) */
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
            max-height: 32vh;
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

        <!-- Stage Konten Slide Informasi (Opsi A: Split 2-Kolom) -->
        <div class="slide-stage">
            @if($slides->count())
            @foreach($slides as $slide)
            <div class="slide {{ $loop->first ? 'active' : '' }}" data-duration="{{ max(3, $slide->durasi) }}">
                
                {{-- Kolom Kiri: Foto / Gambar Utama --}}
                <div class="slide-left-col">
                    @if($slide->gambar)
                    <div class="slide-poster-wrapper">
                        <div class="slide-poster-backdrop" style="background-image: url('{{ $slide->gambar_url }}');"></div>
                        <img src="{{ $slide->gambar_url }}" alt="{{ $slide->judul }}" onerror="this.onerror=null; this.src='{{ asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp') }}';">
                    </div>
                    @else
                    <div class="slide-poster-wrapper">
                        <img src="{{ asset('storage/slides/E6Vbbx4rwXUpvmdD8LodQkbK9x82dYzX723Fb386.webp') }}" alt="{{ $slide->judul }}">
                    </div>
                    @endif
                </div>

                {{-- Kolom Kanan: Kartu Informasi Elegan (Glassmorphism) --}}
                <div class="slide-right-col">
                    <div class="slide-card">
                        <div class="slide-badge">
                            <i class="fas fa-bullhorn"></i>
                            <span>Informasi Kegiatan</span>
                        </div>
                        <div class="judul">
                            {{ $slide->judul }}
                        </div>
                        @if($slide->deskripsi)
                        <div class="deskripsi">{{ $slide->deskripsi }}</div>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
            @else
            <div class="empty">
                Belum ada Slide Informasi yang aktif.
            </div>
            @endif
        </div>

        <!-- Memanggil Komponen Running Text dan Footer Secara Rapi di Bawah -->
        @include('partials.bottom-section')

    </div>

    <!-- SCRIPT UNTUK BACKGROUND SLIDESHOW (EFEK CHECKERBOARD / PAPAN CATUR) -->
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

        const cols = 10;
        const rows = 6;

        if (backgroundImages.length > 0 && bgContainer) {
            bgContainer.style.setProperty('overflow', 'hidden', 'important');
            bgContainer.style.setProperty('position', 'absolute', 'important');
            bgContainer.style.setProperty('z-index', '-1', 'important');
            bgContainer.style.setProperty('perspective', '1000px', 'important');

            bgContainer.style.backgroundImage = `url('${backgroundImages[0]}')`;
            bgContainer.style.backgroundSize = 'cover';
            bgContainer.style.backgroundPosition = 'center';

            function changeBackground() {
                const nextBgIndex = (currentBgIndex + 1) % backgroundImages.length;
                const nextImgUrl = backgroundImages[nextBgIndex];

                const gridContainer = document.createElement('div');
                gridContainer.style.position = 'absolute';
                gridContainer.style.top = '0';
                gridContainer.style.left = '0';
                gridContainer.style.width = '100%';
                gridContainer.style.height = '100%';
                gridContainer.style.display = 'grid';
                gridContainer.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
                gridContainer.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
                bgContainer.appendChild(gridContainer);

                let blocks = [];

                for (let r = 0; r < rows; r++) {
                    for (let c = 0; c < cols; c++) {
                        const block = document.createElement('div');
                        block.style.position = 'relative';
                        block.style.width = '100%';
                        block.style.height = '100%';

                        block.style.backgroundImage = `url('${nextImgUrl}')`;
                        block.style.backgroundSize = '100vw 100vh';

                        const posX = cols === 1 ? 0 : (c / (cols - 1)) * 100;
                        const posY = rows === 1 ? 0 : (r / (rows - 1)) * 100;
                        block.style.backgroundPosition = `${posX}% ${posY}%`;

                        block.style.opacity = '0';
                        block.style.transform = 'rotateY(90deg) scale(0.5)';

                        const delay = Math.random() * 1.5;
                        block.style.transition = `all 0.8s cubic-bezier(0.25, 1, 0.5, 1) ${delay}s`;

                        gridContainer.appendChild(block);
                        blocks.push(block);
                    }
                }

                void gridContainer.offsetWidth;

                blocks.forEach(block => {
                    block.style.opacity = '1';
                    block.style.transform = 'rotateY(0deg) scale(1)';
                });

                setTimeout(() => {
                    bgContainer.style.backgroundImage = `url('${nextImgUrl}')`;
                    if (gridContainer && gridContainer.parentNode) {
                        gridContainer.parentNode.removeChild(gridContainer);
                    }
                }, 2600);

                currentBgIndex = nextBgIndex;
            }

            setInterval(changeBackground, 10000);
        }
    </script>

    <!-- SCRIPT UNTUK ROTASI KONTEN SLIDE INFORMASI (3D BILLBOARD FLIP) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slideElements = document.querySelectorAll('.slide');
            if (!slideElements || slideElements.length <= 1) return;

            let currentSlideIndex = 0;
            let slideTimer = null;

            function showNextSlide() {
                const prevSlide = slideElements[currentSlideIndex];
                currentSlideIndex = (currentSlideIndex + 1) % slideElements.length;
                const nextSlide = slideElements[currentSlideIndex];

                // Efek Billboard Flip: Slide lama memutar keluar (-90deg)
                prevSlide.classList.remove('active');
                prevSlide.classList.add('prev-flip');

                // Siapkan slide berikutnya untuk flip masuk (dari 90deg ke 0deg)
                nextSlide.classList.remove('prev-flip');
                void nextSlide.offsetWidth; // Trigger reflow
                nextSlide.classList.add('active');

                // Bersihkan class prev-flip setelah animasi selesai
                setTimeout(() => {
                    prevSlide.classList.remove('prev-flip');
                }, 900);

                scheduleNextSlide();
            }

            function scheduleNextSlide() {
                if (slideTimer) clearTimeout(slideTimer);
                const currentSlide = slideElements[currentSlideIndex];
                const duration = (parseInt(currentSlide.getAttribute('data-duration'), 10) || 5) * 1000;
                slideTimer = setTimeout(showNextSlide, duration);
            }

            scheduleNextSlide();
        });
    </script>

</body>

</html>