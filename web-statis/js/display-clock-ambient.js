/**
 * Global Unified Hijri + Masehi + Realtime Clock Formatter
 * Format: [Hari, DD MMMM YYYY] <span class="dt-sep">•</span> <span class="hijri-date">[DD BulanHijri YYYY H]</span> <span class="dt-sep">•</span> [HH:mm:ss WIB]
 */
function getStandardHijriDate(date = new Date()) {
    const hijriMonths = [
        '',
        'Muharram',
        'Safar',
        'Rabiul Awal',
        'Rabiul Akhir',
        'Jumadil Awal',
        'Jumadil Akhir',
        'Rajab',
        'Sya\'ban',
        'Ramadhan',
        'Syawal',
        'Dzulqa\'dah',
        'Dzulhijjah'
    ];

    const gregorianToHijriMonthMap = {
        'januari': 1, 'january': 1, 'jan': 1,
        'februari': 2, 'february': 2, 'feb': 2,
        'maret': 3, 'march': 3, 'mar': 3,
        'april': 4, 'apr': 4,
        'mei': 5, 'may': 5,
        'juni': 6, 'june': 6, 'jun': 6,
        'juli': 7, 'july': 7, 'jul': 7,
        'agustus': 8, 'august': 8, 'aug': 8, 'agu': 8,
        'september': 9, 'sep': 9,
        'oktober': 10, 'october': 10, 'okt': 10, 'oct': 10,
        'november': 11, 'nov': 11,
        'desember': 12, 'december': 12, 'des': 12, 'dec': 12
    };

    let day = null;
    let month = null;
    let year = null;

    // 1. Ekstraksi numerik murni via Intl Ummul Qura (menghindari bug Smart TV ICU yang salah menerjemahkan nama bulan dan era)
    try {
        const fmt = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        });

        if (typeof fmt.formatToParts === 'function') {
            const parts = fmt.formatToParts(date);
            for (let i = 0; i < parts.length; i++) {
                const p = parts[i];
                if (p.type === 'day') day = parseInt(p.value, 10);
                if (p.type === 'month') month = parseInt(p.value, 10);
                if (p.type === 'year') year = parseInt(p.value, 10);
            }
        } else {
            const str = fmt.format(date);
            const nums = str.match(/(\d+)[^\d]+(\d+)[^\d]+(\d+)/);
            if (nums) {
                month = parseInt(nums[1], 10);
                day = parseInt(nums[2], 10);
                year = parseInt(nums[3], 10);
            }
        }
    } catch (e) {}

    // 2. Fallback islamic biasa jika umalqura tidak didukung browser TV
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        try {
            const fmt2 = new Intl.DateTimeFormat('en-u-ca-islamic', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            });
            if (typeof fmt2.formatToParts === 'function') {
                const parts = fmt2.formatToParts(date);
                for (let i = 0; i < parts.length; i++) {
                    const p = parts[i];
                    if (p.type === 'day') day = parseInt(p.value, 10);
                    if (p.type === 'month') month = parseInt(p.value, 10);
                    if (p.type === 'year') year = parseInt(p.value, 10);
                }
            }
        } catch (e) {}
    }

    // 3. Tangani format TV lama yang menghasilkan teks "27 Maret 1448 SM"
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        try {
            const tvStr = new Intl.DateTimeFormat('id-ID-u-ca-islamic-umalqura', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            }).format(date);

            const mMatch = tvStr.match(/(\d+)\s+([a-zA-Z']+)\s+(\d+)/);
            if (mMatch) {
                day = parseInt(mMatch[1], 10);
                const mWord = mMatch[2].toLowerCase();
                year = parseInt(mMatch[3], 10);
                if (gregorianToHijriMonthMap[mWord]) {
                    month = gregorianToHijriMonthMap[mWord];
                }
            }
        } catch (e) {}
    }

    // 4. Fallback perhitungan kalender astronomis Islam (Kuwaiti algorithm) jika browser TV tidak mendukung Intl Islamic
    if (!day || !month || !year || month < 1 || month > 12 || year < 1300 || year > 1600) {
        const fallback = computeKuwaitiHijri(date);
        day = fallback.day;
        month = fallback.month;
        year = fallback.year;
    }

    const monthName = hijriMonths[month] || 'Rabiul Awal';
    let result = `${day} ${monthName} ${year} H`;

    // Sanitasi akhir: pastikan tidak ada teks "SM" atau nama bulan Masehi yang tersisa
    result = result.replace(/\bSM\b/gi, 'H');
    for (const [greg, idx] of Object.entries(gregorianToHijriMonthMap)) {
        const reg = new RegExp('\\b' + greg + '\\b', 'gi');
        if (reg.test(result)) {
            result = result.replace(reg, hijriMonths[idx]);
        }
    }
    if (!result.endsWith('H') && !result.endsWith('H.')) {
        result += ' H';
    }

    return result;
}

function computeKuwaitiHijri(date) {
    let day = date.getDate();
    let month = date.getMonth();
    let year = date.getFullYear();

    let m = month + 1;
    let y = year;
    if (m < 3) {
        y -= 1;
        m += 12;
    }

    let a = Math.floor(y / 100);
    let b = 2 - a + Math.floor(a / 4);
    if (y < 1583) b = 0;
    if (y === 1582) {
        if (m > 10) b = -10;
        if (m === 10) {
            b = 0;
            if (day > 4) b = -10;
        }
    }

    let jd = Math.floor(365.25 * (y + 4716)) + Math.floor(30.6001 * (m + 1)) + day + b - 1524;
    b = 0;
    if (jd > 2299160) {
        a = Math.floor((jd - 1867216.25) / 36524.25);
        b = 1 + a - Math.floor(a / 4);
    }
    let bb = jd + b + 1524;
    let cc = Math.floor((bb - 122.1) / 365.25);
    let dd = Math.floor(365.25 * cc);
    let ee = Math.floor((bb - dd) / 30.6001);
    day = (bb - dd) - Math.floor(30.6001 * ee);
    month = ee - 1;
    if (ee > 13) {
        cc += 1;
        month = ee - 13;
    }
    year = cc - 4716;

    let l = jd - 1948440 + 10632;
    let n = Math.floor((l - 1) / 10631);
    l = l - 10631 * n + 354;
    let j = (Math.floor((10985 - l) / 5316)) * (Math.floor((50 * l) / 17719)) + (Math.floor(l / 5670)) * (Math.floor((43 * l) / 15238));
    l = l - (Math.floor((30 - j) / 15)) * (Math.floor((17719 * j) / 50)) - (Math.floor(j / 16)) * (Math.floor((15238 * j) / 43)) + 29;
    let mH = Math.floor((24 * l) / 709);
    let dH = l - Math.floor((709 * mH) / 24);
    let yH = 30 * n + j - 30;

    return { day: dH, month: mH, year: yH };
}

function getStandardMasjidDateTime(now = new Date(), asHtml = true) {
    try {
        const masehi = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        }).format(now);

        const hijri = getStandardHijriDate(now);

        const time = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        }).format(now).replace(/\./g, ':');

        if (asHtml) {
            return `<div class="dt-date-row"><span class="masehi-date">${masehi}</span><span class="dt-sep">•</span><span class="hijri-date">${hijri}</span></div><span class="dt-sep dt-sep-time">•</span><div class="dt-time-row"><span class="jam-time">${time} WIB</span></div>`;
        }
        return `${masehi} • ${hijri} • ${time} WIB`;
    } catch (err) {
        return now.toLocaleString('id-ID');
    }
}

// Auto-bind realtime clock ke #datetime jika elemen tersedia
(function initGlobalClock() {
    function refreshClock() {
        const el = document.getElementById('datetime');
        if (el) {
            el.innerHTML = getStandardMasjidDateTime(new Date(), true);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            refreshClock();
            setInterval(refreshClock, 1000);
        });
    } else {
        refreshClock();
        setInterval(refreshClock, 1000);
    }
})();

// Auto-inject and run Dynamic Ambient Lighting by Prayer Time
(function initDynamicAmbientLighting() {
    function applyDynamicTheme() {
        const now = new Date();
        const totalMins = now.getHours() * 60 + now.getMinutes();

        // 03:30 (210) - 06:00 (360) : Subuh
        // 06:00 (360) - 11:30 (690) : Dhuha / Pagi
        // 11:30 (690) - 15:00 (900) : Dzuhur
        // 15:00 (900) - 17:45 (1065): Ashar
        // 17:45 (1065) - 19:15 (1155): Maghrib
        // 19:15 (1155) - 03:30 (210) : Isya & Malam
        let targetTheme = 'theme-isya';
        if (totalMins >= 210 && totalMins < 360) {
            targetTheme = 'theme-subuh';
        } else if (totalMins >= 360 && totalMins < 690) {
            targetTheme = 'theme-dhuha';
        } else if (totalMins >= 690 && totalMins < 900) {
            targetTheme = 'theme-dzuhur';
        } else if (totalMins >= 900 && totalMins < 1065) {
            targetTheme = 'theme-ashar';
        } else if (totalMins >= 1065 && totalMins < 1155) {
            targetTheme = 'theme-maghrib';
        }

        const themeClasses = ['theme-subuh', 'theme-dhuha', 'theme-dzuhur', 'theme-ashar', 'theme-maghrib', 'theme-isya'];
        if (document.body) {
            themeClasses.forEach(c => {
                if (c === targetTheme) {
                    document.body.classList.add(c);
                } else {
                    document.body.classList.remove(c);
                }
            });
        }

        // Pastikan ambient-glow-layer ada di dalam body
        if (!document.getElementById('ambientGlowLayer') && document.body) {
            const glowLayer = document.createElement('div');
            glowLayer.id = 'ambientGlowLayer';
            glowLayer.className = 'ambient-glow-layer';
            glowLayer.innerHTML = '<div class="ambient-orb-top"></div><div class="ambient-orb-bottom"></div>';
            document.body.appendChild(glowLayer);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            applyDynamicTheme();
            setInterval(applyDynamicTheme, 30000);
        });
    } else {
        applyDynamicTheme();
        setInterval(applyDynamicTheme, 30000);
    }
})();