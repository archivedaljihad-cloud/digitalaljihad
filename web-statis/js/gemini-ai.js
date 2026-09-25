/**
 * web-statis/js/gemini-ai.js
 * Modul Client-Side Google Gemini AI untuk Display & Admin Masjid Jami' Al-Jihad
 * Fitur:
 * 1. AI Copywriter Pengumuman & Agenda Kajian (Structured JSON)
 * 2. Mutiara Hadits Shahih & Hikmah Harian (Otomatis berganti per hari)
 * 3. Tes Koneksi API Key & Model
 * 4. Fallback Offline Anti-Gagal jika tidak ada kuota/jaringan
 */

(function (window) {
    'use strict';

    const STORAGE_KEY_API = 'aljihad_gemini_api_key';
    const STORAGE_KEY_MODEL = 'aljihad_gemini_model';
    const STORAGE_KEY_HIKMAH_CACHE = 'aljihad_daily_hikmah_cache';
    const DEFAULT_MODEL = 'gemini-1.5-flash';

    // 7 Koleksi Hadits Shahih Bawaan (Otentik & Anti-Gagal)
    const OFFLINE_HADITH_COLLECTION = {
        // 0: Ahad
        0: {
            tema: 'Keutamaan Melangkahkan Kaki ke Masjid',
            arab: 'مَنْ غَدَا إِلَى الْمَسْجِدِ أَوْ رَاحَ، أَعَدَّ اللَّهُ لَهُ فِي الْجَنَّةِ نُزُلًا كُلَّمَا غَدَا أَوْ رَاحَ',
            terjemahan: 'Barangsiapa pergi ke masjid pada waktu pagi atau petang hari, niscaya Allah menyediakan baginya tempat tinggal di surga setiap kali ia pergi pada pagi atau petang hari.',
            perawi: 'HR. Bukhari no. 662 & Muslim no. 669',
            hikmah: 'Setiap langkah menuju rumah Allah senantiasa bernilai pahala agung dan meninggikan derajat seorang hamba.'
        },
        // 1: Senin
        1: {
            tema: 'Kemuliaan Menuntut Ilmu Agama',
            arab: 'مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا، سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ',
            terjemahan: 'Barangsiapa menempuh suatu jalan untuk menuntut ilmu (agama), maka Allah akan memudahkan baginya jalan menuju surga.',
            perawi: 'HR. Muslim no. 2699',
            hikmah: 'Menghadiri majelis taklim di masjid adalah jalan lapang yang membimbing kita menuju jannah Allah SWT.'
        },
        // 2: Selasa
        2: {
            tema: 'Kelembutan Akhlak & Menjaga Lisan',
            arab: 'مَنْ كَانَ يُؤْمِنُ بِاللَّهِ وَالْيَوْمِ الْآخِرِ فَلْيَقُلْ خَيْرًا أَوْ لِيَصْمُتْ',
            terjemahan: 'Barangsiapa yang beriman kepada Allah dan hari akhir, hendaklah ia berkata yang baik atau (jika tidak bisa) hendaklah ia diam.',
            perawi: 'HR. Bukhari no. 6018 & Muslim no. 47',
            hikmah: 'Ketenangan hati dan kedamaian ukhuwah bermula dari lisan yang terjaga dari perkataan yang sia-sia.'
        },
        // 3: Rabu
        3: {
            tema: 'Menyebarkan Salam & Kedamaian',
            arab: 'لَا تَدْخُلُونَ الْجَنَّةَ حَتَّى تُؤْمِنُوا، وَلَا تُؤْمِنُوا حَتَّى تَحَابُّوا، أَوَلَا أَدُلُّكُمْ عَلَى شَيْءٍ إِذَا فَعَلْتُمُوهُ تَحَابَبْتُمْ؟ أَفْشُوا السَّلَامَ بَيْنَكُمْ',
            terjemahan: 'Kalian tidak akan masuk surga hingga kalian beriman, dan tidak beriman hingga kalian saling mencintai. Maukah kutunjukkan sesuatu yang jika kalian kerjakan akan saling mencintai? Sebarkanlah salam di antara kalian.',
            perawi: 'HR. Muslim no. 54',
            hikmah: 'Ucapkan salam kepada sesama jamaah untuk menumbuhkan cinta dan keberkahan di dalam masjid kita.'
        },
        // 4: Kamis
        4: {
            tema: 'Kemuliaan Menghilangkan Kesulitan Saudara',
            arab: 'مَنْ نَفَّسَ عَنْ مُؤْمِنٍ كُرْبَةً مِنْ كُرَبِ الدُّنْيَا، نَفَّسَ اللَّهُ عَنْهُ كُرْبَةً مِنْ كُرَبِ يَوْمِ الْقِيَامَةِ',
            terjemahan: 'Barangsiapa melepaskan kesusahan seorang mukmin di dunia, niscaya Allah akan melepaskan kesusahannya di hari kiamat kelak.',
            perawi: 'HR. Muslim no. 2699',
            hikmah: 'Bantulah saudara sesama muslim yang sedang dalam kesulitan, niscaya pertolongan Allah akan selalu menyertai kita.'
        },
        // 5: Jum'at
        5: {
            tema: 'Pahala Sedekah & Shalawat di Hari Jum\'at',
            arab: 'إِنَّ مِنْ أَفْضَلِ أَيَّامِكُمْ يَوْمَ الْجُمُعَةِ، فِيهِ خُلِقَ آدَمُ، وَفِيهِ قُبِضَ... فَأَكْثِرُوا عَلَيَّ مِنَ الصَّلَاةِ فِيهِ، فَإِنَّ صَلَاتَكُمْ مَعْرُوضَةٌ عَلَيَّ',
            terjemahan: 'Sesungguhnya di antara hari kalian yang paling utama adalah hari Jum\'at... Maka perbanyaklah membaca shalawat kepadaku pada hari itu, karena sesungguhnya shalawat kalian disampaikan kepadaku.',
            perawi: 'HR. Abu Dawud no. 1047 & An-Nasa\'i no. 1374',
            hikmah: 'Hari Jum\'at adalah sayyidul ayyam; lipatgandakan sedekah ke kotak infaq masjid dan basahi lisan dengan shalawat.'
        },
        // 6: Sabtu
        6: {
            tema: 'Membangun Keluarga yang Dicintai Allah',
            arab: 'خَيْرُكُمْ خَيْرُكُمْ لِأَهْلِهِ، وَأَنَا خَيْرُكُمْ لِأَهْلِي',
            terjemahan: 'Sebaik-baik kalian adalah yang paling baik kepada keluarganya, dan aku adalah orang yang paling baik di antara kalian kepada keluargaku.',
            perawi: 'HR. Tirmidzi no. 3895 (Hasan Shahih)',
            hikmah: 'Jadikan rumah tangga kita madrasah kebaikan dan teladan cinta kasih sesuai sunnah Rasulullah SAW.'
        }
    };

    const GeminiAI = {
        /**
         * Ambil API Key Gemini yang aktif
         */
        getApiKey: function () {
            const localKey = localStorage.getItem(STORAGE_KEY_API);
            if (localKey && localKey.trim()) return localKey.trim();

            // Cek Supabase Cached Settings
            try {
                const cached = localStorage.getItem('cached_app_settings');
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (parsed.gemini_api_key && parsed.gemini_api_key.trim()) {
                        return parsed.gemini_api_key.trim();
                    }
                }
            } catch (e) {}

            return '';
        },

        /**
         * Simpan API Key Gemini
         */
        setApiKey: function (key) {
            if (key) {
                localStorage.setItem(STORAGE_KEY_API, key.trim());
            } else {
                localStorage.removeItem(STORAGE_KEY_API);
            }
        },

        /**
         * Ambil Model Gemini yang dipilih
         */
        getModel: function () {
            const localModel = localStorage.getItem(STORAGE_KEY_MODEL);
            if (localModel && localModel.trim()) return localModel.trim();

            try {
                const cached = localStorage.getItem('cached_app_settings');
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (parsed.gemini_model && parsed.gemini_model.trim()) {
                        return parsed.gemini_model.trim();
                    }
                }
            } catch (e) {}

            return DEFAULT_MODEL;
        },

        /**
         * Simpan Model Gemini
         */
        setModel: function (model) {
            localStorage.setItem(STORAGE_KEY_MODEL, (model || DEFAULT_MODEL).trim());
        },

        /**
         * Cek apakah API Key telah disetel
         */
        hasApiKey: function () {
            return this.getApiKey().length > 10;
        },

        /**
         * Uji Koneksi ke Google Gemini API
         */
        testConnection: async function (customKey, customModel) {
            const apiKey = (customKey || this.getApiKey() || '').trim();
            const model = (customModel || this.getModel() || DEFAULT_MODEL).trim();

            if (!apiKey) {
                return {
                    success: false,
                    message: 'API Key belum diisi. Silakan masukkan Google Gemini API Key terlebih dahulu.'
                };
            }

            const startTime = Date.now();
            const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/${encodeURIComponent(model)}:generateContent?key=${encodeURIComponent(apiKey)}`;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [
                            {
                                parts: [
                                    { text: 'Jawab dengan 1 kata: "OK" jika kamu terhubung.' }
                                ]
                            }
                        ]
                    })
                });

                const latency = Date.now() - startTime;
                const result = await response.json();

                if (response.ok && result.candidates && result.candidates.length > 0) {
                    return {
                        success: true,
                        latencyMs: latency,
                        model: model,
                        message: `Alhamdulillah! Terhubung ke Google Gemini (${model}) dalam ${latency}ms.`
                    };
                }

                const errorMsg = result?.error?.message || `Gagal menghubungi API Google Gemini (Status ${response.status}).`;
                return {
                    success: false,
                    message: errorMsg
                };
            } catch (err) {
                return {
                    success: false,
                    message: 'Terjadi kesalahan jaringan: ' + (err.message || 'Koneksi gagal.')
                };
            }
        },

        /**
         * AI Copywriter: Buat Draf Pengumuman Terstruktur dari Catatan Mentah
         */
        generateAnnouncement: async function (rawPoints, category, namaMasjid) {
            const cat = category || 'Kajian Rutin';
            const masjid = namaMasjid || 'Masjid Jami\' Al-Jihad';
            const apiKey = this.getApiKey();
            const model = this.getModel();

            if (!apiKey) {
                return this.fallbackAnnouncement(rawPoints, cat, masjid);
            }

            const prompt = `Kamu adalah asisten sekretaris DKM ${masjid} yang sangat santun, profesional, dan berbahasa Indonesia Islami yang baik.
Berdasarkan poin-poin kegiatan berikut:
"${rawPoints}"
(Kategori: ${cat})

Tolong susun draf pengumuman resmi masjid dan kembalikan HANYA dalam format JSON valid tanpa format markdown code block \`\`\`json:
{
  "judul": "Judul kegiatan yang menarik, ringkas, dan formal (maks 80 karakter)",
  "pemateri": "Nama pemateri/ustadz lengkap dengan gelar jika ada di teks (kosongkan string jika tidak ada)",
  "waktu": "Waktu pelaksanaan yang rapi (contoh: Ba'da Maghrib s/d Isya / Pukul 08.30 WIB)",
  "tempat": "Tempat pelaksanaan (default: Ruang Utama ${masjid})",
  "isi": "Paragraf pengumuman lengkap bernuansa ajakan ibadah yang santun, diawali salam atau mukaddimah singkat, rincian materi, dan ditutup dengan ajakan/doa kehadiran (2-3 paragraf singkat)",
  "running_text": "Versi ringkas 1 kalimat padat untuk teks berjalan layar TV (maksimal 130 karakter)"
}`;

            const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/${encodeURIComponent(model)}:generateContent?key=${encodeURIComponent(apiKey)}`;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [
                            {
                                parts: [{ text: prompt }]
                            }
                        ],
                        generationConfig: {
                            temperature: 0.4,
                            responseMimeType: 'application/json'
                        }
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    let rawText = result?.candidates?.[0]?.content?.parts?.[0]?.text;
                    if (rawText) {
                        // Bersihkan markdown ```json jika ada
                        rawText = rawText.replace(/```json/gi, '').replace(/```/g, '').trim();
                        const parsed = JSON.parse(rawText);
                        if (parsed && parsed.judul && parsed.isi) {
                            return {
                                success: true,
                                data: parsed,
                                source: 'gemini_ai',
                                model: model
                            };
                        }
                    }
                }
            } catch (err) {
                console.warn('Gemini generateAnnouncement error, beralih ke fallback:', err);
            }

            return this.fallbackAnnouncement(rawPoints, cat, masjid);
        },

        /**
         * Fallback Offline Anti-Gagal untuk Pengumuman
         */
        fallbackAnnouncement: function (rawPoints, category, namaMasjid) {
            let judul = (rawPoints || 'Kegiatan Masjid').trim();
            // Ambil kalimat pertama atau potong maks 65 karakter
            const firstLine = judul.split('\n')[0];
            judul = firstLine.length > 60 ? firstLine.substring(0, 57) + '...' : firstLine;
            // Capitalize
            judul = judul.charAt(0).toUpperCase() + judul.slice(1);

            return {
                success: true,
                data: {
                    judul: judul,
                    pemateri: '',
                    waktu: "Ba'da Maghrib s/d Selesai",
                    tempat: "Ruang Utama " + namaMasjid,
                    isi: `Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nHadirilah kegiatan ${category} di ${namaMasjid}.\nPoin kegiatan: ${rawPoints}.\n\nMari kita ramaikan majelis kebaikan ini bersama keluarga tercinta. Semoga Allah SWT senantiasa meridhoi langkah kita. Barakallahu fiikum.`,
                    running_text: `Hadirilah ${judul} di ${namaMasjid}. Mari ramaikan majelis ilmu bersama keluarga.`
                },
                source: 'fallback_offline'
            };
        },

        /**
         * Ambil Hadits & Mutiara Hikmah Harian
         */
        getDailyHikmah: async function (forceRefresh = false) {
            const now = new Date();
            const dateStr = now.toISOString().split('T')[0]; // 'YYYY-MM-DD'
            const dayOfWeek = now.getDay(); // 0..6
            const daysIndo = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
            const monthsIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayName = daysIndo[dayOfWeek];
            const fullDateStr = `${now.getDate()} ${monthsIndo[now.getMonth()]} ${now.getFullYear()}`;

            // Cek Cache Lokal Browser jika tidak di-force
            if (!forceRefresh) {
                try {
                    const cachedRaw = localStorage.getItem(STORAGE_KEY_HIKMAH_CACHE);
                    if (cachedRaw) {
                        const cachedObj = JSON.parse(cachedRaw);
                        if (cachedObj && cachedObj.cacheDate === dateStr && cachedObj.arab) {
                            return cachedObj;
                        }
                    }
                } catch (e) {}
            }

            const apiKey = this.getApiKey();
            const model = this.getModel();

            // Jika ada API Key, tanyakan ke Gemini
            if (apiKey) {
                const prompt = `Sebagai ahli hadits dan sastra Islam, pilihkan TEPAT 1 (SATU) Hadits Shahih otentik (dari Shahih Bukhari, Shahih Muslim, Sunan Abu Dawud, Tirmidzi, atau An-Nasa'i) yang sangat relevan untuk hari ${dayName}.
Kriteria tema per hari:
- Senin: Keutamaan puasa sunnah, menuntut ilmu, atau tawadhu'.
- Selasa: Adab bertetangga, akhlak mulia, dan silaturahmi.
- Rabu: Menjaga lisan, kejujuran, dan berprasangka baik.
- Kamis: Keutamaan puasa sunnah dan persiapan menyambut hari Jum'at.
- Jum'at: Kemuliaan sedekah, shalawat atas Nabi SAW, dan adab Jum'at.
- Sabtu: Keharmonisan keluarga, berbakti kepada orang tua, dan mendidik anak.
- Ahad: Ikhlas beribadah, sholat berjamaah di masjid, dan dzikrullah.

Kembalikan HANYA dalam format JSON valid tanpa format markdown code block \`\`\`json:
{
  "tema": "Tema singkat (contoh: Keutamaan Bersedekah di Hari Jum'at)",
  "arab": "Teks matan hadits dalam tulisan Arab berharakat lengkap dan jelas (tanpa rawi awal, langsung sabda Rasulullah SAW)",
  "terjemahan": "Terjemahan bahasa Indonesia yang jelas, fasih, dan mudah dipahami jamaah",
  "perawi": "Nama perawi sahih (contoh: HR. Bukhari no. 1421 & Muslim no. 1010)",
  "hikmah": "1 kalimat mutiara intisari tadabbur hadits ini untuk diamalkan jamaah hari ini",
  "hari": "${dayName}",
  "tanggal": "${fullDateStr}"
}`;

                const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/${encodeURIComponent(model)}:generateContent?key=${encodeURIComponent(apiKey)}`;

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            contents: [{ parts: [{ text: prompt }] }],
                            generationConfig: {
                                temperature: 0.3,
                                responseMimeType: 'application/json'
                            }
                        })
                    });

                    if (response.ok) {
                        const result = await response.json();
                        let rawText = result?.candidates?.[0]?.content?.parts?.[0]?.text;
                        if (rawText) {
                            rawText = rawText.replace(/```json/gi, '').replace(/```/g, '').trim();
                            const parsed = JSON.parse(rawText);
                            if (parsed && parsed.arab && parsed.terjemahan) {
                                parsed.cacheDate = dateStr;
                                parsed.source = 'gemini_ai';
                                localStorage.setItem(STORAGE_KEY_HIKMAH_CACHE, JSON.stringify(parsed));
                                return parsed;
                            }
                        }
                    }
                } catch (err) {
                    console.warn('Gemini getDailyHikmah error, beralih ke hadits bawaan:', err);
                }
            }

            // Fallback Hadits Shahih Harian Bawaan
            const offline = Object.assign({}, OFFLINE_HADITH_COLLECTION[dayOfWeek] || OFFLINE_HADITH_COLLECTION[5]);
            offline.hari = dayName;
            offline.tanggal = fullDateStr;
            offline.cacheDate = dateStr;
            offline.source = 'offline_collection';

            try {
                localStorage.setItem(STORAGE_KEY_HIKMAH_CACHE, JSON.stringify(offline));
            } catch (e) {}

            return offline;
        }
    };

    window.GeminiAI = GeminiAI;

})(window);
