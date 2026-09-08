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

        .slide {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #fff;
            text-align: center;
            padding: 190px 40px 90px 40px;
            box-sizing: border-box;
        }

        .slide.active {
            display: flex;
        }

        .slide img {
            max-width: 90%;
            max-height: 55vh;
            object-fit: contain;
        }

        /* --- KODE PENGATURAN TEKS ATAS (JUDUL) --- */
        .judul {
            margin-top: 50px;
            margin-bottom: 0px;
            font-size: 2rem;
            font-weight: bold;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);
        }

        /* --- KODE PENGATURAN TEKS BAWAH (DESKRIPSI) --- */
        .deskripsi {
            margin-top: -15px !important;
            font-size: 1.25rem !important;
            font-weight: bold !important;
            line-height: 1.5 !important;
            color: #ffd700 !important;
            white-space: pre-line !important;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95), 0 0 15px rgba(0, 0, 0, 0.8) !important;
            text-align: center !important;
            max-width: 85% !important;
            margin-left: auto !important;
            margin-right: auto !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
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

        /* --- GAYA UNTUK HEADER H1 DAN H3 (HIJAU EMAS) --- */
        .header-section {
            position: absolute;
            top: 25px;
            left: 0;
            width: 100%;
            text-align: center;
            z-index: 50;
        }

        .header-section h1 {
            font-family: 'Masking Renta', sans-serif !important;
            font-size: 3.2rem !important;
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
            margin-bottom: -15px !important;
            margin-top: 0;
        }

        .header-section h3.sub-header {
            font-family: 'Poppins', sans-serif !important;
            font-size: 1.25rem !important;
            font-weight: 500 !important;
            letter-spacing: 4px !important;
            color: #ffffff !important;
            text-transform: uppercase !important;
            opacity: 0.95 !important;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8) !important;
            margin-top: 10px !important;
        }
    </style>
</head>

<body>
    <div class="display-background"></div>
    <div class="display-overlay"></div>

    <div class="display-content" style="height: 100%; width: 100%; position: relative; display: flex; flex-direction: column; justify-content: space-between;">

        <div class="header-section">
            <h1>
                @php
                // 1. Cek dari variabel controller
                $appName = $settings['nama_aplikasi'] ?? ($settings->nama_aplikasi ?? null);

                // 2. Jika kosong, coba ambil dari Cache aplikasi (biasanya disimpan admin di sini)
                if (empty($appName)) {
                $appName = cache('nama_aplikasi') ?? cache('settings')['nama_aplikasi'] ?? null;
                }

                // 3. Jika masih kosong, coba ambil dari tabel singular 'setting' (tanpa 's')
                if (empty($appName)) {
                try {
                $settingData = \DB::table('setting')->first();
                $appName = $settingData->nama_aplikasi ?? $settingData->nama_masjid ?? null;
                } catch (\Exception $e) {
                // Abaikan jika tabel tidak ada
                }
                }

                // 4. Fallback terakhir jika semuanya kosong
                if (empty($appName)) {
                $appName = 'MASJID JAMI AL- JIHAD';
                }
                @endphp
                {{ $appName }}
            </h1>
            <h3 class="sub-header">SISTEM INFORMASI DIGITAL</h3>
        </div>

        <div style="flex: 1; position: relative; width: 100%; overflow: hidden;">
            @if($slides->count())
            @foreach($slides as $slide)
            <div class="slide {{ $loop->first ? 'active' : '' }}" data-duration="{{ max(3, $slide->durasi) }}">
                @if($slide->gambar)
                <img src="{{ asset('storage/' . $slide->gambar) }}" alt="{{ $slide->judul }}" onerror="this.onerror=null; this.src='{{ asset('storage/slides/1gdpqFYCyv7Sv0qLDTpyxSjMnknbVEM9OLVOjPM3.png') }}';">
                @endif
                <div class="judul">
                    {{ $slide->judul }}
                </div>
                @if($slide->deskripsi)
                <div class="deskripsi">
                    {{ $slide->deskripsi }}
                </div>
                @endif
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

</body>

</html>