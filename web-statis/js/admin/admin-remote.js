/**
 * MODUL ADMIN: admin-remote.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.45
 */

        // LAYANAN REMOT TV & DIAGNOSTIK JARAK JAUH (REALTIME CLOUD)
        // =====================================================
        let remotePresenceSubscription = null;
        let lastKnownDeviceState = null;
        let remoteCommandHistory = [];

        function initRemoteControlView() {
            renderRemoteCommandLog();
            startRemotePresenceMonitor();
        }

        function refreshRemoteMonitor() {
            if (typeof SupabaseDB !== 'undefined' && typeof SupabaseDB.getRemoteChannel === 'function') {
                const ch = SupabaseDB.getRemoteChannel();
                if (ch) {
                    try {
                        const st = ch.presenceState();
                        updateTvPresenceUI(st);
                    } catch(e) {}
                }
            }
            startRemotePresenceMonitor(true);
        }

        function startRemotePresenceMonitor(forceRestart = false) {
            if (typeof SupabaseDB === 'undefined' || typeof SupabaseDB.subscribeDevicePresence !== 'function') {
                console.warn('[RemoteAdmin] SupabaseDB device presence belum siap');
                return;
            }

            if (forceRestart && remotePresenceSubscription) {
                try {
                    const st = remotePresenceSubscription.presenceState();
                    if (st) updateTvPresenceUI(st);
                } catch(e) {}
                return;
            }

            if (!remotePresenceSubscription) {
                remotePresenceSubscription = SupabaseDB.subscribeDevicePresence((devices) => {
                    updateTvPresenceUI(devices);
                });
            }
        }

        function updateTvPresenceUI(rawState) {
            const statusBadge = document.getElementById('tvStatusBadge');
            const statusDot = document.getElementById('tvStatusDot');
            const statusText = document.getElementById('tvStatusText');
            const currentSlideEl = document.getElementById('tvCurrentSlideName');
            const currentSlideUrlEl = document.getElementById('tvCurrentSlideUrl');
            const tvResolutionEl = document.getElementById('tvResolutionText');
            const tvUserAgentEl = document.getElementById('tvUserAgentText');
            const tvLastSeenEl = document.getElementById('tvLastSeenText');
            const tvPlaybackStatusEl = document.getElementById('tvPlaybackStatusText');
            const btnPauseResume = document.getElementById('btnRemotePauseResume');

            if (!statusBadge) return;

            let list = [];
            if (Array.isArray(rawState)) {
                list = rawState;
            } else if (rawState && typeof rawState === 'object') {
                // Supabase presenceState() mengembalikan object: { [key]: [presencePayload, ...] }
                Object.values(rawState).forEach(val => {
                    if (Array.isArray(val)) {
                        list.push(...val);
                    } else if (val && typeof val === 'object') {
                        list.push(val);
                    }
                });
            }

            // Filter perangkat TV aktif
            const tvDevices = list.filter(d => d && (d.type === 'tv-display' || (d.device_id && String(d.device_id).startsWith('TV'))));

            if (tvDevices.length > 0) {
                const activeTv = tvDevices[0];
                lastKnownDeviceState = activeTv;

                if (statusDot) statusDot.className = 'remote-pulse-dot online';
                if (statusBadge) {
                    statusBadge.className = 'badge badge-success font-weight-bold px-3 py-2 mr-2 mb-1';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> TV ONLINE (' + tvDevices.length + ' Layar Aktif)';
                }
                if (statusText) {
                    statusText.textContent = 'Terhubung ke Cloud';
                    statusText.title = 'Terhubung ke Cloud Realtime';
                }

                const slideTitle = activeTv.currentSlideTitle || activeTv.current_slide_title || activeTv.currentSlide || activeTv.current_slide || 'Slide Utama';
                const slideUrl = activeTv.currentSlide || activeTv.current_slide || 'slides/utama.html';

                if (currentSlideEl) {
                    currentSlideEl.textContent = slideTitle;
                    currentSlideEl.title = slideTitle;
                }
                if (currentSlideUrlEl) {
                    currentSlideUrlEl.textContent = slideUrl;
                    currentSlideUrlEl.title = slideUrl;
                }

                const resText = activeTv.resolution || ((activeTv.width && activeTv.height) ? `${activeTv.width} x ${activeTv.height} px` : '1920 x 1080 px');
                if (tvResolutionEl) {
                    tvResolutionEl.textContent = resText;
                    tvResolutionEl.title = resText;
                }

                const uaText = (activeTv.userAgent || activeTv.user_agent || 'Smart TV Browser');
                if (tvUserAgentEl) {
                    tvUserAgentEl.textContent = uaText;
                    tvUserAgentEl.title = uaText;
                }

                if (tvLastSeenEl) tvLastSeenEl.textContent = 'Aktif (Sinyal Realtime)';

                const isPaused = (activeTv.isPaused === true || activeTv.is_paused === true);
                if (tvPlaybackStatusEl) {
                    tvPlaybackStatusEl.textContent = isPaused ? 'DIJEDA (Freeze)' : 'MEMUTAR OTOMATIS';
                    tvPlaybackStatusEl.className = isPaused ? 'remote-stat-val text-warning text-truncate' : 'remote-stat-val text-success text-truncate';
                }

                if (btnPauseResume) {
                    if (isPaused) {
                        btnPauseResume.innerHTML = '<i class="fas fa-play mr-2"></i> Lanjutkan Rotasi TV';
                        btnPauseResume.className = 'btn btn-success font-weight-bold btn-remote-dpad mr-2 mb-2';
                    } else {
                        btnPauseResume.innerHTML = '<i class="fas fa-pause mr-2"></i> Jeda Rotasi (Freeze)';
                        btnPauseResume.className = 'btn btn-warning font-weight-bold btn-remote-dpad mr-2 mb-2';
                    }
                }
            } else {
                if (statusDot) statusDot.className = 'remote-pulse-dot offline';
                if (statusBadge) {
                    statusBadge.className = 'badge badge-secondary font-weight-bold px-3 py-2 mr-2 mb-1';
                    statusBadge.innerHTML = '<i class="fas fa-power-off mr-1"></i> TV STANDBY / MENUNGGU';
                }
                if (statusText) statusText.textContent = 'Menunggu sinyal TV...';
                if (currentSlideEl) currentSlideEl.textContent = 'Menunggu data TV...';
                if (currentSlideUrlEl) currentSlideUrlEl.textContent = '-';
                if (tvLastSeenEl) tvLastSeenEl.textContent = 'Belum ada koneksi';
            }
        }

        async function kirimPerintahRemote(command, data = {}) {
            try {
                const activeUser = (currentUser || (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getCurrentUser === 'function' ? AdminAuth.getCurrentUser() : null));
                const senderName = activeUser ? (activeUser.name || activeUser.username || 'Super Admin') : 'Super Admin';
                
                addRemoteLogEntry(command, data, 'Mengirim...');

                if (typeof SupabaseDB === 'undefined' || typeof SupabaseDB.sendRemoteCommand !== 'function') {
                    throw new Error('SupabaseDB.sendRemoteCommand belum terdefinisi');
                }

                const res = await SupabaseDB.sendRemoteCommand(command, data, senderName);
                if (res && (res.success || res.command)) {
                    updateLastRemoteLogStatus('Berhasil Terkirim (<100ms)');
                    console.log(`[RemoteAdmin] Perintah "${command}" terkirim dengan sukses.`);
                } else {
                    updateLastRemoteLogStatus('Gagal: ' + (res.error || 'Timeout'));
                    alert('Gagal mengirim perintah: ' + (res.error || 'Koneksi timeout'));
                }
            } catch (err) {
                console.error('[RemoteAdmin] Error:', err);
                updateLastRemoteLogStatus('Error: ' + err.message);
                alert('Kesalahan pengiriman: ' + err.message);
            }
        }

        function toggleRemotePause() {
            const isCurrentlyPaused = lastKnownDeviceState && lastKnownDeviceState.isPaused;
            const targetCmd = isCurrentlyPaused ? 'RESUME' : 'PAUSE';
            kirimPerintahRemote(targetCmd);
        }

        function reloadRemoteTV() {
            if (confirm('Apakah Anda yakin ingin memuat ulang (RELOAD) browser layar TV masjid dari jarak jauh?\n\nLayar TV akan otomatis me-refresh dalam 2 detik. Ini sangat berguna jika TV hang atau perlu pembaharuan memori.')) {
                kirimPerintahRemote('RELOAD');
            }
        }

        function pingRemoteTV() {
            kirimPerintahRemote('PING', { timestamp: Date.now() });
            alert('Sinyal tes PING telah dipancarkan ke Layar TV!');
        }

        function jumpToSlideDirect(slideUrl, slideTitle) {
            kirimPerintahRemote('JUMP', {
                url: slideUrl,
                title: slideTitle
            });
        }

        function kirimSiaranDarurat() {
            const inputTeks = document.getElementById('inputEmergencyText');
            const selectTheme = document.getElementById('selectEmergencyTheme');
            const selectDuration = document.getElementById('selectEmergencyDuration');

            if (!inputTeks || !inputTeks.value.trim()) {
                alert('Silakan tuliskan teks pengumuman darurat terlebih dahulu!');
                if (inputTeks) inputTeks.focus();
                return;
            }

            const text = inputTeks.value.trim();
            const theme = selectTheme ? selectTheme.value : 'gold';
            const duration = selectDuration ? (parseInt(selectDuration.value) || 0) : 30000;

            kirimPerintahRemote('EMERGENCY_ALERT', {
                text: text,
                theme: theme,
                duration: duration
            });

            alert('Siaran darurat berhasil dipancarkan ke TV masjid seketika!');
        }

        function hapusSiaranDarurat() {
            kirimPerintahRemote('CLEAR_ALERT');
            alert('Perintah penutupan siaran darurat telah dikirim ke layar TV.');
        }

        function fallbackCCTV(target) {
            let targetUrl = 'slides/cctv.html';
            let label = 'CCTV Utama Masjid';
            if (target === 'mekah') {
                targetUrl = 'slides/live-mekah.html';
                label = 'Live Ka\'bah Masjidil Haram Mekah';
            } else if (target === 'madinah') {
                targetUrl = 'slides/live-madinah.html';
                label = 'Live Nabawi Madinah';
            }

            if (confirm(`Alihkan tampilan CCTV ke: "${label}"?\n\nLangkah ini berguna jika kamera CCTV lokal masjid sedang bermasalah atau jaringan internal offline.`)) {
                kirimPerintahRemote('JUMP', {
                    url: targetUrl,
                    title: label
                });
            }
        }

        function addRemoteLogEntry(cmd, data, status) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            remoteCommandHistory.unshift({
                time: timeStr,
                timestamp: now.toISOString(),
                command: cmd,
                payload: data,
                status: status || 'Mengirim...'
            });
            if (remoteCommandHistory.length > 200) remoteCommandHistory.pop();
            renderRemoteCommandLog();
        }

        function updateLastRemoteLogStatus(newStatus) {
            if (remoteCommandHistory.length > 0) {
                remoteCommandHistory[0].status = newStatus;
                renderRemoteCommandLog();
            }
        }

        function renderRemoteCommandLog() {
            const body = document.getElementById('remoteLogTerminalBody');
            const countBadge = document.getElementById('badgeLogCount');
            const metaStatus = document.getElementById('terminalMetaStatus');
            if (!body) return;

            const total = remoteCommandHistory.length;
            if (countBadge) countBadge.textContent = `${total} baris`;
            if (metaStatus) metaStatus.textContent = total > 0 ? `${total} entri tercatat` : 'Live WebSocket';

            if (total === 0) {
                body.innerHTML = `
                    <div class="terminal-empty">
                        <i class="fas fa-terminal mr-2"></i> Belum ada aktivitas perintah remote pada sesi ini.
                    </div>
                `;
                return;
            }

            body.innerHTML = remoteCommandHistory.map((item, idx) => {
                let tag = 'CMD';
                let tagClass = 'log-tag-cmd';
                const cmd = (item.command || '').toUpperCase();

                if (cmd.includes('EMERGENCY')) {
                    tag = 'EMG';
                    tagClass = 'log-tag-emg';
                } else if (cmd.includes('PING')) {
                    tag = 'PNG';
                    tagClass = 'log-tag-png';
                } else if (cmd === 'NEXT' || cmd === 'PREV' || cmd === 'PAUSE' || cmd === 'RESUME') {
                    tag = 'ROT';
                    tagClass = 'log-tag-rot';
                } else if (cmd === 'RELOAD') {
                    tag = 'SYS';
                    tagClass = 'log-tag-sys';
                } else if (cmd.includes('CLEAR')) {
                    tag = 'CLR';
                    tagClass = 'log-tag-clr';
                }

                let payloadStr = '';
                if (item.payload && typeof item.payload === 'object' && Object.keys(item.payload).length > 0) {
                    const entries = Object.entries(item.payload).map(([k, v]) => {
                        let valStr = typeof v === 'string' ? `"${v}"` : String(v);
                        if (valStr.length > 50) valStr = valStr.substring(0, 47) + '..."';
                        return `<span class="log-payload">${escapeHtml(k)}</span>=<span class="log-payload-val">${escapeHtml(valStr)}</span>`;
                    });
                    payloadStr = ` <span class="log-arrow">&gt;</span> ` + entries.join(' ');
                } else if (typeof item.payload === 'string' && item.payload !== '{}' && item.payload !== '') {
                    payloadStr = ` <span class="log-arrow">&gt;</span> <span class="log-payload">${escapeHtml(item.payload)}</span>`;
                }

                let statusClass = 'log-status-success';
                const st = item.status || '';
                if (st.includes('Gagal') || st.includes('Error')) {
                    statusClass = 'log-status-error';
                } else if (st.includes('Mengirim') || st.includes('Menunggu')) {
                    statusClass = 'log-status-wait';
                }

                return `
                    <div class="log-line">
                        <span class="log-time">${item.time}</span>
                        <span class="log-tag ${tagClass}">[${tag}]</span>
                        <span class="log-cmd-name">${escapeHtml(cmd)}</span>
                        ${payloadStr}
                        <span class="log-arrow">&rarr;</span>
                        <span class="${statusClass}">status=${escapeHtml(st)}</span>
                    </div>
                `;
            }).join('');
        }

        function clearRemoteCommandLog() {
            if (!remoteCommandHistory || remoteCommandHistory.length === 0) {
                alert('Log aktivitas saat ini sudah kosong.');
                return;
            }
            if (confirm('Bersihkan seluruh riwayat log perintah remote pada sesi ini?')) {
                remoteCommandHistory = [];
                renderRemoteCommandLog();
            }
        }

        function downloadRemoteCommandLog() {
            if (!remoteCommandHistory || remoteCommandHistory.length === 0) {
                alert('Log aktivitas masih kosong. Belum ada perintah remote yang tercatat untuk diunduh.');
                return;
            }

            const now = new Date();
            const tglFormatted = now.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            const jamFormatted = now.toLocaleTimeString('id-ID');
            const operatorName = currentUser ? (currentUser.name || currentUser.email) : 'Administrator';

            let logText = [
                '========================================================================================',
                '                   MASJID JAMI\' AL-JIHAD - LOG AKTIVITAS PERINTAH REMOTE TV             ',
                '========================================================================================',
                `Waktu Ekspor  : ${tglFormatted}, ${jamFormatted} WIB`,
                `Operator      : ${operatorName}`,
                `Total Perintah: ${remoteCommandHistory.length} entri tercatat`,
                '----------------------------------------------------------------------------------------',
                'WAKTU     TAG    PERINTAH              RINCIAN PAYLOAD                   STATUS PANCARAN',
                '----------------------------------------------------------------------------------------'
            ];

            remoteCommandHistory.slice().reverse().forEach((item, i) => {
                let tag = 'CMD';
                const cmd = (item.command || '').toUpperCase();
                if (cmd.includes('EMERGENCY')) tag = 'EMG';
                else if (cmd.includes('PING')) tag = 'PNG';
                else if (cmd === 'NEXT' || cmd === 'PREV' || cmd === 'PAUSE' || cmd === 'RESUME') tag = 'ROT';
                else if (cmd === 'RELOAD') tag = 'SYS';
                else if (cmd.includes('CLEAR')) tag = 'CLR';

                const pStr = typeof item.payload === 'object' ? JSON.stringify(item.payload) : (item.payload || '{}');
                logText.push(`${item.time}  [${tag}]  ${cmd.padEnd(20)}  > ${pStr}  ->  ${item.status}`);
            });

            logText.push('========================================================================================');
            logText.push('               Dokumen ini diunduh secara resmi dari Dashboard Smart Signage            ');
            logText.push('========================================================================================');

            const blob = new Blob([logText.join('\r\n')], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const fileDate = now.toISOString().replace(/[-:]/g, '').split('.')[0].replace('T', '-');
            a.href = url;
            a.download = `remote-tv-log-${fileDate}.log`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function confirmLogout(event) {
            if (event) {
                try {
                    event.preventDefault();
                    event.stopPropagation();
                } catch(e) {}
            }

            try {
                if (typeof $ !== 'undefined') {
                    $('.dropdown-menu').removeClass('show');
                }
            } catch(e) {}

            // Konfirmasi langsung browser yang 100% responsif & bebas konflik modal di semua perangkat
            const yakin = confirm("Apakah Anda yakin ingin mengakhiri sesi dan keluar dari Panel Admin Masjid Jami' Al-Jihad?");
            if (yakin) {
                eksekusiLogout();
            }
        }

        function eksekusiLogout() {
            try {
                localStorage.removeItem('aljihad_auth_user');
                sessionStorage.clear();
            } catch (e) {
                console.warn('Gagal menghapus session storage:', e);
            }

            if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.logout === 'function') {
                AdminAuth.logout('login.html?action=logout');
                return;
            }

            window.location.replace('login.html?action=logout');
        }

        // =====================================================