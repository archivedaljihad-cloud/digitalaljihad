/**
 * anti-idle.js - Engine Anti-Idle & Screen Keep-Awake 24 Jam Nonstop
 * Sistem Informasi Digital Display Masjid Jami' Al-Jihad
 * 
 * Fitur:
 * 1. Screen Wake Lock API resmi W3C (navigator.wakeLock.request('screen')).
 * 2. Auto-Reacquire saat visibilityState kembali menjadi 'visible' atau window focus.
 * 3. User Gesture Hook: Menjamin wakeLock otomatis aktif segera setelah klik/touch/remote TV pertama.
 * 4. Micro-Video Looper Fallback: Untuk browser Smart TV legacy (Tizen, WebOS, Android TV jadul) 
 *    yang belum memiliki implementasi navigator.wakeLock.
 * 5. Heartbeat Interval: Memeriksa dan memperbarui status aktif setiap 30 detik.
 */

(function () {
    'use strict';

    let wakeLockSentinel = null;
    let isRequesting = false;
    let fallbackVideo = null;

    // 1. Minta Screen Wake Lock via API Standar Browser Modern
    async function requestWakeLock() {
        if (isRequesting) return;
        if ('wakeLock' in navigator) {
            isRequesting = true;
            try {
                wakeLockSentinel = await navigator.wakeLock.request('screen');
                console.log('🛡️ [Anti-Idle] Screen Wake Lock AKTIF (Layar TV terkunci menyala 24 jam).');

                wakeLockSentinel.addEventListener('release', () => {
                    console.log('ℹ️ [Anti-Idle] Screen Wake Lock dilepaskan oleh OS/Browser.');
                    wakeLockSentinel = null;
                });
            } catch (err) {
                console.warn('⚠️ [Anti-Idle] Gagal mengaktifkan Screen Wake Lock API:', err.name, err.message);
                // Aktifkan fallback jika browser menolak/belum mendukung sepenuhnya
                activateFallbackLooper();
            } finally {
                isRequesting = false;
            }
        } else {
            console.log('ℹ️ [Anti-Idle] navigator.wakeLock tidak tersedia, beralih ke Fallback Looper.');
            activateFallbackLooper();
        }
    }

    // 2. Fallback Looper untuk Smart TV jadul (Invisible 1px Muted Video Loop)
    function activateFallbackLooper() {
        if (fallbackVideo) return;
        try {
            fallbackVideo = document.createElement('video');
            fallbackVideo.setAttribute('playsinline', '');
            fallbackVideo.setAttribute('webkit-playsinline', '');
            fallbackVideo.setAttribute('muted', '');
            fallbackVideo.setAttribute('loop', '');
            fallbackVideo.muted = true;
            fallbackVideo.loop = true;
            fallbackVideo.style.position = 'fixed';
            fallbackVideo.style.bottom = '0';
            fallbackVideo.style.right = '0';
            fallbackVideo.style.width = '1px';
            fallbackVideo.style.height = '1px';
            fallbackVideo.style.opacity = '0.001';
            fallbackVideo.style.pointerEvents = 'none';
            fallbackVideo.style.zIndex = '-9999';

            // Ultra-tiny 1-pixel transparent H.264 MP4 base64 video loop
            fallbackVideo.src = 'data:video/mp4;base64,AAAAHGZ0eXBtcDQyAAAAAG1wNDJpc29tYXZjMQAAABBmcmVlAAAABG1kYXQAAAA/bW9vdgAAAGxtdmhkAAAAAAAAAAAAAAAAAAAC6gAAAu4AAQAAAQAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgAAAHB0cmFrAAAAXHRraGQAAAADAAAAAAAAAAAAAAABAAAAAAAC7gAAAAAAAAAAAAAAAAAAAAAAAAEAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAABAAAAAAAAAAAAAAAAAABAAAAAABRlZHRzAAAAHGVsc3QAAAAAAAAAAQAAAu4AAAAAAAEAAAAAAZJtZGlhAAAAIG1kaGQAAAAAAAAAAAAAAAAAAGQAAAAwAAAAMAAAACAAAABoZGwAAAAAAAExbmdyAAAAAB1zb3VuZAAAAAAAAAAAAAAAADEyMzQ1Njc4OQAAAVFtaW5mAAAAHGRpbmYAAAAUcmVmZgAAAAx1cmwgAAAAAQAAAChzdGJsAAAAGHN0c2QAAAAAAAAAAQAAAAAAAAAAAAAAYAAAAAAAYAAAABNzdHRzAAAAAAAAAAEAAAABAAAAMAAAABRzdHN6AAAAAAAAAAAAAAABAAAAHAAAABhzdGNvAAAAAAAAAAEAAAAAAAAAAAA=';

            document.body.appendChild(fallbackVideo);

            const playPromise = fallbackVideo.play();
            if (playPromise !== undefined) {
                playPromise.catch(() => {
                    // Jika diblokir oleh autoplay policy, tunggu sentuhan/klik/remote pertama
                    const startPlay = () => {
                        if (fallbackVideo) fallbackVideo.play().catch(() => {});
                    };
                    ['click', 'keydown', 'touchstart'].forEach(evt => {
                        document.addEventListener(evt, startPlay, { once: true, passive: true });
                    });
                });
            }
            console.log('🛡️ [Anti-Idle] Fallback video looper berhasil dipasang.');
        } catch (e) {
            console.warn('⚠️ [Anti-Idle] Gagal memasang fallback video:', e);
        }
    }

    // 3. Pasang Event Listeners: Auto Re-acquire saat Visibility Change & Focus
    document.addEventListener('visibilitychange', async () => {
        if (document.visibilityState === 'visible' && !wakeLockSentinel) {
            await requestWakeLock();
        }
    });

    window.addEventListener('focus', async () => {
        if (!wakeLockSentinel) {
            await requestWakeLock();
        }
    });

    // 4. Pastikan aktif saat interaksi pertama (touch, click, keydown remote TV)
    const onFirstInteraction = async () => {
        if (!wakeLockSentinel) {
            await requestWakeLock();
        }
        if (fallbackVideo && fallbackVideo.paused) {
            fallbackVideo.play().catch(() => {});
        }
    };
    ['click', 'touchstart', 'keydown'].forEach(evt => {
        document.addEventListener(evt, onFirstInteraction, { passive: true });
    });

    // 5. Inisialisasi saat DOM siap & Heartbeat berkala tiap 30 detik
    function initAntiIdle() {
        requestWakeLock();
        setInterval(() => {
            if (document.visibilityState === 'visible' && !wakeLockSentinel) {
                requestWakeLock();
            }
        }, 30000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAntiIdle);
    } else {
        initAntiIdle();
    }

    // Ekspor ke window agar bisa diakses jika diperlukan
    window.AntiIdle = {
        request: requestWakeLock,
        isActive: () => !!wakeLockSentinel,
        fallbackActive: () => !!fallbackVideo
    };
})();
