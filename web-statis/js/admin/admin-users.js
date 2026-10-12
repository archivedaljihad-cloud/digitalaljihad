/**
 * MODUL ADMIN: admin-users.js
 * Digital Signage Masjid Jami' Al-Jihad
 * Versi: aljihad-signage-v5.8.46
 */

        // MANAJEMEN AKUN PENGGUNA (KHUSUS SUPER ADMIN)
        // =====================================================
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderUsersTable() {
            try {
                const tbody = document.getElementById('tbodyUsersManagement');
                if (!tbody) return;

                const activeUser = (currentUser || (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getCurrentUser === 'function' ? AdminAuth.getCurrentUser() : null));
                const isSuperAdmin = (activeUser && (activeUser.role === 'admin' || activeUser.role_id === 3));
                const alertNonAdmin = document.getElementById('alertNonSuperAdminUsers');
                const badgeIndicator = document.getElementById('badgeSuperAdminOnlyIndicator');
                const btnTambahAkun = document.getElementById('btnTambahAkunBaru');

                if (alertNonAdmin) {
                    if (isSuperAdmin) {
                        alertNonAdmin.classList.add('d-none');
                    } else {
                        alertNonAdmin.classList.remove('d-none');
                    }
                }

                if (badgeIndicator) {
                    if (isSuperAdmin) {
                        badgeIndicator.className = 'badge badge-warning font-weight-bold px-3 py-2';
                        badgeIndicator.innerHTML = '<i class="fas fa-shield-alt mr-1"></i> Mode Super Admin: Akses Penuh Aktif';
                    } else {
                        badgeIndicator.className = 'badge badge-secondary font-weight-bold px-3 py-2';
                        badgeIndicator.innerHTML = '<i class="fas fa-lock mr-1"></i> Mode Read-Only (Hanya Super Admin)';
                    }
                }

                if (btnTambahAkun) {
                    if (isSuperAdmin) {
                        btnTambahAkun.classList.remove('d-none');
                    } else {
                        btnTambahAkun.classList.add('d-none');
                    }
                }

                let users = [];
                if (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getUsers === 'function') {
                    try {
                        users = AdminAuth.getUsers();
                    } catch (e) {
                        console.warn('Gagal memanggil AdminAuth.getUsers:', e);
                    }
                }
                if (!Array.isArray(users) || users.length === 0) {
                    users = (typeof DEFAULT_AUTH_USERS !== 'undefined') ? DEFAULT_AUTH_USERS : [];
                }

                const badgeCount = document.getElementById('badgeTotalUsersCount');
                if (badgeCount) badgeCount.textContent = `${users.length} Akun Terdaftar`;

                tbody.innerHTML = '';

                users.forEach((u, idx) => {
                    const tr = document.createElement('tr');

                    // Role badge & deskripsi hak akses
                    let roleBadge = '';
                    let scopeDesc = '';
                    if (u.role === 'admin') {
                        roleBadge = '<span class="badge badge-warning font-weight-bold px-2 py-1"><i class="fas fa-crown mr-1"></i> Super Admin</span>';
                        scopeDesc = 'Akses Penuh 100% (Display TV, Pengaturan Slide, Keuangan, Pengguna, Sistem)';
                    } else if (u.role === 'bendahara') {
                        roleBadge = '<span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-wallet mr-1"></i> Bendahara</span>';
                        scopeDesc = 'Buku Kas Utama, Kas Ambulance, Infaq & Donasi, Laporan Keuangan';
                    } else {
                        roleBadge = '<span class="badge badge-info font-weight-bold px-2 py-1"><i class="fas fa-tv mr-1"></i> Operator TV</span>';
                        scopeDesc = 'Jadwal Sholat, Petugas Jum\'at, Pengumuman, Teks Berjalan, Slide TV';
                    }

                    // Tombol aksi Edit & Hapus Akun
                    let actionHtml = '';
                    if (isSuperAdmin) {
                        const isSelf = activeUser && (String(activeUser.id) === String(u.id) || activeUser.email === u.email);
                        const deleteBtn = isSelf 
                            ? `<span class="badge badge-secondary py-1 px-2 ml-1" title="Akun Anda yang sedang aktif login" style="font-size: 11px;"><i class="fas fa-user-check"></i> Anda</span>`
                            : `<button class="btn btn-sm btn-outline-danger font-weight-bold ml-1" onclick="hapusAkunUser(${u.id}, '${escapeHtml(u.name)}')" title="Hapus Akun Pengurus" style="border-radius: 6px; padding: 4px 8px;">
                                   <i class="fas fa-trash-alt"></i>
                               </button>`;

                        actionHtml = `
                            <div class="d-flex align-items-center justify-content-center" style="gap: 4px;">
                                <button class="btn btn-sm btn-warning font-weight-bold" onclick="bukaModalEditUser(${u.id})" title="Edit Akun" style="box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4); border-radius: 6px; padding: 4px 10px;">
                                    <i class="fas fa-user-edit mr-1"></i> Edit
                                </button>
                                ${deleteBtn}
                            </div>
                        `;
                    } else {
                        actionHtml = `
                            <button class="btn btn-sm btn-secondary disabled" disabled title="Hanya Super Admin yang berhak mengedit" style="cursor: not-allowed; opacity: 0.5; border-radius: 6px;">
                                <i class="fas fa-lock mr-1"></i> Terkunci
                            </button>
                        `;
                    }

                    const initialLetter = (u.name || 'U').trim().charAt(0).toUpperCase();
                    const cleanUsername = u.username || (u.email ? u.email.split('@')[0] : 'user');

                    tr.innerHTML = `
                        <td class="font-weight-bold text-center text-muted" style="vertical-align: middle;">${u.id || (idx + 1)}</td>
                        <td style="vertical-align: middle;">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-2" style="width:32px; height:32px; border-radius:50%; background:${u.color || '#ffd700'}; color:#04261a; font-weight:800; font-size: 0.85rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                                    ${initialLetter}
                                </div>
                                <div style="line-height: 1.25;">
                                    <strong class="text-gray-900" style="font-size: 0.9rem;">${escapeHtml(u.name || '-')}</strong>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="d-flex flex-column" style="gap: 3px;">
                                <div>
                                    <span class="badge badge-light px-2 py-1 font-weight-bold text-dark" style="border: 1px solid #cbd5e1; background: #f8fafc; font-size: 0.8rem;">
                                        <i class="fas fa-at text-muted mr-1"></i>${escapeHtml(cleanUsername)}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted font-weight-bold" style="font-size: 0.8rem; font-family: monospace;">
                                        <i class="fas fa-envelope mr-1 text-secondary"></i>${escapeHtml(u.email || '-')}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle;">${roleBadge}</td>
                        <td style="vertical-align: middle;"><small class="text-muted" style="line-height: 1.35; display: inline-block; max-width: 280px;">${scopeDesc}</small></td>
                        <td class="text-center" style="vertical-align: middle;"><span class="badge badge-success px-2 py-1">Aktif</span></td>
                        <td class="text-center" style="vertical-align: middle;">${actionHtml}</td>
                    `;

                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error('Error saat renderUsersTable:', err);
            }
        }

        // =====================================================
        // MODAL TAMBAH PENGURUS BARU
        // =====================================================
        function bukaModalTambahUser() {
            const activeUser = (currentUser || (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getCurrentUser === 'function' ? AdminAuth.getCurrentUser() : null));
            if (!activeUser || (activeUser.role !== 'admin' && activeUser.role_id !== 3)) {
                alert('Akses Ditolak: Hanya Super Admin yang berhak menambahkan akun pengguna!');
                return;
            }

            const form = document.getElementById('formTambahUser');
            if (form) form.reset();

            const pwdInput = document.getElementById('tambahUserPassword');
            const icon = document.getElementById('iconToggleTambahPassword');
            if (pwdInput) pwdInput.type = 'password';
            if (icon) icon.className = 'fas fa-eye';

            const alertErr = document.getElementById('alertTambahUserError');
            if (alertErr) {
                alertErr.classList.add('d-none');
                alertErr.textContent = '';
            }

            $('#modalTambahUser').modal('show');
        }

        function autoFillUsernameTambah(name) {
            const unInput = document.getElementById('tambahUserUsername');
            if (!unInput) return;
            // Hanya isi otomatis jika username masih kosong atau baru mengetik
            const clean = (name || '').toLowerCase()
                .replace(/^(bpk\.|ust\.|h\.|ibu|haji|ustadz|kyai|bapak)\s+/i, '')
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 15);
            if (clean && (!unInput.dataset.manualEdit || unInput.dataset.manualEdit === '0')) {
                unInput.value = clean;
            }
        }

        // Tandai jika username diubah manual oleh pengguna
        document.addEventListener('DOMContentLoaded', () => {
            const unInput = document.getElementById('tambahUserUsername');
            if (unInput) {
                unInput.addEventListener('input', () => {
                    unInput.dataset.manualEdit = '1';
                });
            }
        });

        function toggleTambahPasswordVisibility() {
            const pwdInput = document.getElementById('tambahUserPassword');
            const icon = document.getElementById('iconToggleTambahPassword');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                pwdInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        async function simpanAkunUserBaru(event) {
            event.preventDefault();

            if (!currentUser || (currentUser.role !== 'admin' && currentUser.role_id !== 3)) {
                alert('Akses Ditolak: Hanya Super Admin yang berhak menambahkan akun baru!');
                return;
            }

            const name = document.getElementById('tambahUserName').value.trim();
            const username = document.getElementById('tambahUserUsername').value.trim();
            const email = document.getElementById('tambahUserEmail').value.trim();
            const password = document.getElementById('tambahUserPassword').value.trim();
            const role = document.getElementById('tambahUserRole')?.value || 'petugas';

            const btnSubmit = document.getElementById('btnSubmitTambahUser');
            const alertErr = document.getElementById('alertTambahUserError');

            try {
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                }

                const newUser = await AdminAuth.createUser({ name, username, email, password, role });

                $('#modalTambahUser').modal('hide');

                // Render ulang tabel
                renderUsersTable();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Akun Berhasil Dibuat!',
                        text: `Akun pengurus "${newUser.name}" (${newUser.role_label}) berhasil ditambahkan dan siap digunakan untuk login.`,
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonText: 'Alhamdulillah',
                        confirmButtonColor: '#10b981',
                        background: '#04261a',
                        color: '#ffd700'
                    });
                } else {
                    alert(`Akun pengurus "${newUser.name}" (${newUser.role_label}) berhasil ditambahkan!`);
                }
            } catch (err) {
                if (alertErr) {
                    alertErr.classList.remove('d-none');
                    alertErr.textContent = err.message || 'Gagal menambahkan akun pengurus baru.';
                } else {
                    alert(err.message || 'Gagal menambahkan akun pengurus baru.');
                }
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-user-plus mr-1"></i> Simpan & Buat Akun';
                }
            }
        }

        // =====================================================
        // MODAL EDIT PENGURUS
        // =====================================================
        function bukaModalEditUser(userId) {
            const activeUser = (currentUser || (typeof AdminAuth !== 'undefined' && typeof AdminAuth.getCurrentUser === 'function' ? AdminAuth.getCurrentUser() : null));
            if (!activeUser || (activeUser.role !== 'admin' && activeUser.role_id !== 3)) {
                alert('Akses Ditolak: Hanya Super Admin yang berhak mengedit akun pengguna!');
                return;
            }

            const users = AdminAuth.getUsers();
            const targetUser = users.find(u => String(u.id) === String(userId));
            if (!targetUser) {
                alert('Pengguna tidak ditemukan!');
                return;
            }

            document.getElementById('editUserId').value = targetUser.id;
            document.getElementById('editUserName').value = targetUser.name || '';
            document.getElementById('editUserUsername').value = targetUser.username || (targetUser.email ? targetUser.email.split('@')[0] : '');
            document.getElementById('editUserEmail').value = targetUser.email || '';
            document.getElementById('editUserPassword').value = '';
            document.getElementById('editUserRole').value = targetUser.role || 'petugas';

            const alertErr = document.getElementById('alertEditUserError');
            if (alertErr) {
                alertErr.classList.add('d-none');
                alertErr.textContent = '';
            }

            // Reset password visibility
            const pwdInput = document.getElementById('editUserPassword');
            const icon = document.getElementById('iconToggleEditPassword');
            if (pwdInput) pwdInput.type = 'password';
            if (icon) icon.className = 'fas fa-eye';

            $('#modalEditUser').modal('show');
        }

        function toggleEditPasswordVisibility() {
            const pwdInput = document.getElementById('editUserPassword');
            const icon = document.getElementById('iconToggleEditPassword');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                pwdInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        async function simpanPerubahanUser(event) {
            event.preventDefault();

            if (!currentUser || (currentUser.role !== 'admin' && currentUser.role_id !== 3)) {
                alert('Akses Ditolak: Hanya Super Admin yang berhak mengedit akun pengguna!');
                return;
            }

            const userId = document.getElementById('editUserId').value;
            const name = document.getElementById('editUserName').value.trim();
            const username = document.getElementById('editUserUsername').value.trim();
            const email = document.getElementById('editUserEmail').value.trim();
            const password = document.getElementById('editUserPassword').value.trim();
            const role = document.getElementById('editUserRole')?.value || 'petugas';

            const btnSubmit = document.getElementById('btnSubmitEditUser');
            const alertErr = document.getElementById('alertEditUserError');

            try {
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                }

                await AdminAuth.updateUser(userId, { name, username, email, password, role });

                $('#modalEditUser').modal('hide');

                // Render ulang tabel & state profil aktif
                renderUsersTable();
                applyCurrentUserState(AdminAuth.getCurrentUser());

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diperbarui!',
                        text: `Kredensial akun "${name}" (Username: ${username || email.split('@')[0]}) berhasil diperbarui oleh Super Admin.`,
                        timer: 2500,
                        showConfirmButton: false,
                        background: '#04261a',
                        color: '#ffd700'
                    });
                } else {
                    alert(`Kredensial akun "${name}" berhasil diperbarui oleh Super Admin.`);
                }
            } catch (err) {
                if (alertErr) {
                    alertErr.classList.remove('d-none');
                    alertErr.textContent = err.message || 'Gagal menyimpan perubahan pengguna.';
                } else {
                    alert(err.message || 'Gagal menyimpan perubahan pengguna.');
                }
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Perubahan';
                }
            }
        }

        // =====================================================
        // HAPUS AKUN PENGURUS
        // =====================================================
        async function hapusAkunUser(userId, userName) {
            if (!currentUser || (currentUser.role !== 'admin' && currentUser.role_id !== 3)) {
                alert('Akses Ditolak: Hanya Super Admin yang berhak menghapus akun pengguna!');
                return;
            }

            const executeDelete = async () => {
                try {
                    await AdminAuth.deleteUser(userId);
                    renderUsersTable();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Akun Terhapus!',
                            text: `Akun pengurus "${userName}" telah berhasil dihapus dari sistem.`,
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#04261a',
                            color: '#ffd700'
                        });
                    } else {
                        alert(`Akun pengurus "${userName}" telah berhasil dihapus dari sistem.`);
                    }
                } catch (err) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: err.message || 'Terjadi kesalahan saat menghapus akun.',
                            background: '#04261a',
                            color: '#ef4444'
                        });
                    } else {
                        alert(err.message || 'Terjadi kesalahan saat menghapus akun.');
                    }
                }
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Akun Pengurus?',
                    text: `Apakah Anda yakin ingin menghapus akun "${userName}"? Pengurus ini tidak akan bisa login lagi ke sistem dashboard.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus Akun',
                    cancelButtonText: 'Batal',
                    background: '#04261a',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeDelete();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menghapus akun "${userName}"?`)) {
                    executeDelete();
                }
            }
        }
