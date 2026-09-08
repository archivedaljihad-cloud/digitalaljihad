<!-- resources/views/partials/bottom-section.blade.php -->
<div class="fixed-bottom-section" style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 9999; background: transparent; padding: 4px 0 2px 0; box-sizing: border-box; pointer-events: none;">
    @php
        // Pastikan ambil data setting terbaru dari controller, cache, atau model AppSetting langsung
        if (empty($settings)) {
            $settings = \App\Models\AppSetting::first() ?? cache('settings');
        }

        $rawText = $settings['running_text'] ?? ($settings->running_text ?? null);
        if (empty($rawText)) {
            $rawText = \App\Models\AppSetting::value('running_text');
        }

        $footerText = $settings['footer'] ?? ($settings->footer ?? null);
        if (empty($footerText)) {
            $footerText = \App\Models\AppSetting::value('footer');
        }

        $runningTextList = [];
        if (!empty($rawText)) {
            $lines = preg_split('/\r\n|\r|\n/', $rawText);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $runningTextList[] = $trimmed;
                }
            }
        }
        if (empty($runningTextList)) {
            $runningTextList = [
                "🌙 \"Luruskan dan rapatkan shaf, karena lurusnya shaf merupakan kesempurnaan sholat.\" (HR. Bukhari & Muslim)",
                "📱 Mohon menonaktifkan atau mengalihkan HP ke mode hening selama berada di dalam masjid.",
                "🤲 \"Barangsiapa membangun masjid karena Allah, maka Allah bangunkan baginya rumah di surga.\" (HR. Muslim)",
                "🧹 Jagalah selalu kebersihan dan kesucian masjid kita tercinta.",
                "💧 Hematlah dalam penggunaan air wudhu demi kelestarian bersama."
            ];
        }
    @endphp

    <style>
        .running-track-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
            position: relative;
            height: 38px;
            display: flex;
            align-items: center;
        }

        .running-single-item {
            position: absolute;
            left: 0;
            white-space: nowrap;
            will-change: transform;
            font-size: 1.45rem;
            font-weight: 600;
            color: #ffffff !important;
            letter-spacing: 0.5px;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.95), 0 0 12px rgba(0, 0, 0, 0.85);
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100vw);
        }

        .running-single-item i {
            color: #ffd700;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.9));
        }

        .footer-credit {
            text-align: center;
            padding: 2px 0;
            font-size: 0.85rem;
            color: #ffd700;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.95);
            letter-spacing: 0.5px;
        }
    </style>

    <div class="running-track-container">
        <div id="runningTextModelA" class="running-single-item">
            <i class="fas fa-bullhorn"></i>
            <span id="runningTextModelAContent"></span>
        </div>
    </div>
    
    <div class="footer-credit">
        {!! !empty($footerText) ? $footerText : '' !!}
    </div>

    <script>
        (function() {
            const messages = {!! json_encode(array_values($runningTextList)) !!};
            if (!messages || messages.length === 0) return;

            const el = document.getElementById('runningTextModelA');
            const content = document.getElementById('runningTextModelAContent');
            if (!el || !content) return;

            let currentIndex = 0;

            function startNextMessage(index) {
                content.innerHTML = messages[index];

                requestAnimationFrame(() => {
                    const screenWidth = window.innerWidth || document.documentElement.clientWidth || 1920;
                    const textWidth = el.offsetWidth || 600;
                    const totalDistance = screenWidth + textWidth;

                    // Kecepatan membaca layar TV: ~110 pixel per detik
                    const speed = 110; 
                    const duration = Math.max(10, totalDistance / speed);

                    el.style.transition = 'none';
                    el.style.transform = 'translateX(' + screenWidth + 'px)';

                    requestAnimationFrame(() => {
                        el.style.transition = 'transform ' + duration + 's linear';
                        el.style.transform = 'translateX(-' + (textWidth + 40) + 'px)';

                        const handleEnd = () => {
                            el.removeEventListener('transitionend', handleEnd);
                            // Lanjut ke pesan berikutnya (Model A: satu per satu bergantian)
                            currentIndex = (currentIndex + 1) % messages.length;
                            setTimeout(() => {
                                startNextMessage(currentIndex);
                            }, 600);
                        };

                        el.addEventListener('transitionend', handleEnd, { once: true });
                    });
                });
            }

            startNextMessage(0);
        })();
    </script>
</div>