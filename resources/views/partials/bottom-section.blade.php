<!-- resources/views/partials/bottom-section.blade.php -->
<div style="position: fixed; bottom: 0; left: 0; width: 100%; z-index: 9999; background: transparent; padding: 6px 0; box-sizing: border-box;">
    @php
        $runningTextValue = $settings['running_text'] ?? ($settings->running_text ?? null);
        if (empty($runningTextValue)) {
            $runningTextValue = "Mari penuhi panggilan Allah. Jangan tunda kewajiban kita.";
        }
    @endphp

    <style>
        .running-text-fixed {
            display: inline-block;
            animation: marqueeFixed 50s linear infinite !important;
            color: #fff !important;
            font-weight: 600;
            white-space: nowrap;
            /* Efek bayangan teks agar tetap jelas terbaca tanpa kotak hitam */
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.9), 0 0 10px rgba(0, 0, 0, 0.7);
        }
        @keyframes marqueeFixed {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>

    <div style="width: 100%; overflow: hidden; white-space: nowrap; box-sizing: border-box;">
        <div class="running-text-fixed">
            <i class="fas fa-bullhorn" style="color: #ffc107; margin-right: 8px; filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.9));"></i> {!! $runningTextValue !!}
        </div>
    </div>
    
    <div style="text-align: center; padding: 4px; font-size: 0.85rem; color: #ffd700; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);">
        {!! $settings['footer'] ?? 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System' !!}
    </div>
</div>