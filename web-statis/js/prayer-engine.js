// web-statis/js/prayer-engine.js
/**
 * Prayer Engine Display Masjid (Client-Side)
 * Menghitung waktu sholat, fase Prayer Mode (Tarhim, Adzan, Iqamah, Sholat/Khutbah),
 * dan hitung mundur sholat berikutnya secara akurat di browser.
 */

(function (window) {
    const PHASES = {
        INACTIVE: 'inactive',
        COUNTDOWN: 'countdown',
        ADZAN: 'adzan',
        IQAMAH: 'iqamah',
        PRAYER: 'prayer',
        KHUTBAH: 'khutbah'
    };

    const NON_PRAYER_TIMES = ['imsak', 'imsyak', 'terbit', 'syuruk', 'shuruk', 'sunrise', 'dhuha', 'duha'];

    const PrayerEngine = {
        PHASES,

        /**
         * Evaluasi status Prayer Mode saat ini
         * @param {Object} settings
         * @param {Array} jadwalList
         * @param {Object} jumatPetugas
         * @param {Date} [customNow]
         */
        getPrayerState(settings, jadwalList, jumatPetugas, customNow) {
            const now = customNow || new Date();
            const setting = settings || {};

            if (!setting.prayer_mode_enabled) {
                return {
                    active: false,
                    phase: PHASES.INACTIVE,
                    next_prayer: this.calculateNextPrayer(jadwalList, now)
                };
            }

            if (!jadwalList || jadwalList.length === 0) {
                return {
                    active: false,
                    phase: PHASES.INACTIVE,
                    next_prayer: null
                };
            }

            const beforeAdzan = parseInt(setting.prayer_mode_before_adzan) || 5;
            const adzanDuration = parseInt(setting.prayer_mode_adzan_duration) || 5;
            const iqamahDuration = parseInt(setting.prayer_mode_iqamah_duration) || 10;
            const prayerDuration = parseInt(setting.prayer_mode_duration) || 10;
            const jumatDuration = parseInt(setting.prayer_mode_jumat_duration) || 50;

            const isFriday = now.getDay() === 5; // 5 = Friday

            // Filter non-fardhu
            const fardhuList = jadwalList.filter(item => {
                if (!item || !item.nama_sholat || !item.waktu) return false;
                const clean = item.nama_sholat.toLowerCase().trim();
                return !NON_PRAYER_TIMES.includes(clean);
            });

            for (const item of fardhuList) {
                const cleanName = item.nama_sholat.toLowerCase().trim();
                const isDzuhur = ['dzuhur', 'zuhur', 'dhuhur', "jum'at", 'jumat'].includes(cleanName);
                const isFridayPrayer = isFriday && isDzuhur;

                // Parse waktu sholat hari ini
                const [hStr, mStr, sStr] = item.waktu.split(':');
                const adzanTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(hStr), parseInt(mStr), parseInt(sStr || 0));

                const countdownStart = new Date(adzanTime.getTime() - beforeAdzan * 60 * 1000);
                const adzanEnd = new Date(adzanTime.getTime() + adzanDuration * 60 * 1000);

                if (isFridayPrayer) {
                    const jumatEnd = new Date(adzanEnd.getTime() + jumatDuration * 60 * 1000);

                    if (now < countdownStart || now >= jumatEnd) {
                        continue;
                    }

                    let phase = PHASES.INACTIVE;
                    let remaining = 0;

                    if (now < adzanTime) {
                        phase = PHASES.COUNTDOWN;
                        remaining = Math.round((adzanTime - now) / 1000);
                    } else if (now < adzanEnd) {
                        phase = PHASES.ADZAN;
                        remaining = Math.round((adzanEnd - now) / 1000);
                    } else {
                        phase = PHASES.KHUTBAH;
                        remaining = Math.round((jumatEnd - now) / 1000);
                    }

                    return {
                        active: true,
                        isFriday: true,
                        phase,
                        prayer: "SHOLAT JUM'AT",
                        currentPrayer: { nama_sholat: "SHOLAT JUM'AT", waktu: item.waktu },
                        jumatPetugas: jumatPetugas || null,
                        remaining: Math.max(0, remaining),
                        next_prayer: { name: "JUM'AT", time: item.waktu.substring(0, 5), remaining: Math.max(0, remaining) }
                    };
                } else {
                    const iqamahEnd = new Date(adzanEnd.getTime() + iqamahDuration * 60 * 1000);
                    const prayerEnd = new Date(iqamahEnd.getTime() + prayerDuration * 60 * 1000);

                    if (now < countdownStart || now >= prayerEnd) {
                        continue;
                    }

                    let phase = PHASES.INACTIVE;
                    let remaining = 0;

                    if (now < adzanTime) {
                        phase = PHASES.COUNTDOWN;
                        remaining = Math.round((adzanTime - now) / 1000);
                    } else if (now < adzanEnd) {
                        phase = PHASES.ADZAN;
                        remaining = Math.round((adzanEnd - now) / 1000);
                    } else if (now < iqamahEnd) {
                        phase = PHASES.IQAMAH;
                        remaining = Math.round((iqamahEnd - now) / 1000);
                    } else {
                        phase = PHASES.PRAYER;
                        remaining = Math.round((prayerEnd - now) / 1000);
                    }

                    return {
                        active: true,
                        isFriday: false,
                        phase,
                        prayer: item.nama_sholat,
                        currentPrayer: item,
                        jumatPetugas: null,
                        remaining: Math.max(0, remaining),
                        next_prayer: { name: item.nama_sholat.toUpperCase(), time: item.waktu.substring(0, 5), remaining: Math.max(0, remaining) }
                    };
                }
            }

            return {
                active: false,
                phase: PHASES.INACTIVE,
                next_prayer: this.calculateNextPrayer(jadwalList, now)
            };
        },

        /**
         * Hitung sholat berikutnya dari waktu sekarang
         */
        calculateNextPrayer(jadwalList, now) {
            if (!jadwalList || jadwalList.length === 0) return null;

            const isFriday = now.getDay() === 5;
            const fardhuList = jadwalList.filter(item => {
                if (!item || !item.nama_sholat || !item.waktu) return false;
                const clean = item.nama_sholat.toLowerCase().trim();
                return !NON_PRAYER_TIMES.includes(clean);
            });

            let nearest = null;
            let minDiff = Infinity;

            for (const item of fardhuList) {
                const [h, m, s] = item.waktu.split(':');
                const pTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(h), parseInt(m), parseInt(s || 0));
                const diff = (pTime - now) / 1000;

                if (diff > 0 && diff < minDiff) {
                    minDiff = diff;
                    let displayName = item.nama_sholat.toUpperCase();
                    if (isFriday && ['DZUHUR', 'ZUHUR', 'DHUHUR'].includes(displayName)) {
                        displayName = "JUM'AT";
                    }
                    nearest = {
                        name: displayName,
                        time: item.waktu.substring(0, 5),
                        remaining: Math.round(diff)
                    };
                }
            }

            // Jika semua jadwal hari ini sudah lewat, sholat berikutnya adalah Subuh esok hari
            if (!nearest && fardhuList.length > 0) {
                const subuhItem = fardhuList.find(i => i.nama_sholat.toLowerCase().includes('subuh')) || fardhuList[0];
                const [h, m, s] = subuhItem.waktu.split(':');
                const tomorrowSubuh = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, parseInt(h), parseInt(m), parseInt(s || 0));
                const diff = Math.round((tomorrowSubuh - now) / 1000);

                nearest = {
                    name: subuhItem.nama_sholat.toUpperCase(),
                    time: subuhItem.waktu.substring(0, 5),
                    remaining: Math.max(0, diff)
                };
            }

            return nearest;
        }
    };

    window.PrayerEngine = PrayerEngine;
})(window);
