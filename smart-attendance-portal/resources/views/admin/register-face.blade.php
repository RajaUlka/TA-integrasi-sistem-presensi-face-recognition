<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Wajah | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(0.95);opacity:0.6} }
        @keyframes photoIn { from{opacity:0;transform:scale(0.8)} to{opacity:1;transform:scale(1)} }

        .fade-up{animation:fadeUp 0.5s ease both;}
        .delay-1{animation-delay:0.08s} .delay-2{animation-delay:0.16s} .delay-3{animation-delay:0.24s}

        .shimmer-text { background:linear-gradient(90deg,#2563eb,#6366f1,#8b5cf6,#2563eb); background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; animation:shimmer 3s linear infinite; }
        .form-input { width:100%; padding:0.75rem 1rem; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; font-size:14px; color:#1e293b; outline:none; transition:all 0.25s; font-family:'Plus Jakarta Sans',sans-serif; box-sizing:border-box; }
        .form-input:focus { background:#fff; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.08); }
        .form-input:disabled { opacity:0.4; cursor:not-allowed; }
        .form-label { display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:6px; letter-spacing:0.03em; }

        .cam-btn { padding:0.75rem 1rem; border:none; border-radius:12px; font-weight:700; font-size:14px; cursor:pointer; transition:all 0.25s; display:flex; align-items:center; justify-content:center; gap:8px; }
        .cam-btn-start { background:#1e293b; color:white; border:1px solid rgba(148,163,184,0.2); }
        .cam-btn-start:hover { background:#334155; }
        .cam-btn-start.active { background:#16a34a; color:white; cursor:default; }
        .cam-btn-capture { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:white; box-shadow:0 4px 16px rgba(79,70,229,0.3); }
        .cam-btn-capture:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 8px 20px rgba(79,70,229,0.4); }
        .cam-btn-capture:disabled { opacity:0.4; cursor:not-allowed; }
        .cam-btn-capture.done { background:#16a34a; }

        .submit-btn { width:100%; padding:0.875rem; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:white; border:none; border-radius:12px; font-weight:700; font-size:15px; cursor:pointer; transition:all 0.3s; box-shadow:0 4px 16px rgba(79,70,229,0.25); display:flex; align-items:center; justify-content:center; gap:8px; }
        .submit-btn:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 8px 24px rgba(79,70,229,0.35); }
        .submit-btn:disabled { opacity:0.4; cursor:not-allowed; }
        .submit-btn.ready { background:linear-gradient(135deg,#16a34a,#15803d); box-shadow:0 4px 16px rgba(22,163,74,0.3); }

        .photo-thumb { width:56px; height:56px; object-fit:cover; border-radius:10px; border:2px solid #6366f1; animation:photoIn 0.3s ease; box-shadow:0 2px 8px rgba(99,102,241,0.2); flex-shrink:0; }

        .progress-dot { width:10px; height:10px; border-radius:50%; background:#e2e8f0; transition:all 0.3s; }
        .progress-dot.active { background:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.2); }
        .progress-dot.done { background:#22c55e; }

        .table-row { transition:background 0.15s; }
        .table-row:hover { background:#f8fafc; }
        .delete-btn { padding:7px 12px; background:#fff0f0; color:#ef4444; border:1px solid #fecaca; border-radius:9px; cursor:pointer; transition:all 0.2s; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
        .delete-btn:hover { background:#fee2e2; transform:scale(1.02); }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh;">

    @include('layouts.admin-nav')

    <main style="max-width:1200px; margin:0 auto; padding:2rem 1.5rem;">

        <!-- Alerts -->
        @if(session('success'))
        <div class="fade-up" style="margin-bottom:1.25rem;padding:1rem 1.25rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;display:flex;align-items:center;gap:10px;">
            <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size:14px;font-weight:600;color:#15803d;">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="fade-up" style="margin-bottom:1.25rem;padding:1rem 1.25rem;background:#fef2f2;border:1px solid #fecaca;border-radius:14px;display:flex;align-items:center;gap:10px;">
            <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
            <span style="font-size:14px;font-weight:600;color:#dc2626;">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Header -->
        <div class="fade-up" style="margin-bottom:2rem;text-align:center;">
            <h1 style="font-size:1.75rem;font-weight:800;color:#0f172a;margin:0 0 6px;">
                Registrasi <span class="shimmer-text">Biometrik Wajah</span>
            </h1>
            <p style="font-size:14px;color:#94a3b8;margin:0;">Ambil 5 foto wajah dari berbagai sudut untuk data AI yang akurat.</p>
        </div>

        <!-- Main Grid -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;" class="reg-grid">

            <!-- LEFT: Camera -->
            <div class="fade-up delay-1" style="background:#0f172a;border-radius:24px;border:1px solid rgba(99,102,241,0.15);overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.12);">

                <!-- Camera viewport -->
                <div style="position:relative;width:100%;aspect-ratio:4/3;background:#08101e;display:flex;align-items:center;justify-content:center;">
                    <video id="video-feed" style="width:100%;height:100%;object-fit:cover;display:none;" autoplay muted playsinline></video>

                    <div id="camera-placeholder" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;">
                        <div style="width:72px;height:72px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <svg width="32" height="32" fill="none" stroke="#6366f1" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.6;"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </div>
                        <p style="font-size:14px;color:#475569;font-weight:500;">Kamera belum aktif</p>
                    </div>

                    <!-- Photo count overlay -->
                    <div id="photo-count-overlay" style="display:none;position:absolute;top:12px;right:12px;background:rgba(15,23,42,0.85);border:1px solid rgba(99,102,241,0.3);padding:6px 12px;border-radius:999px;">
                        <span id="count-text" style="font-size:13px;font-weight:700;color:#a5b4fc;">0 / 5</span>
                    </div>
                </div>

                <!-- Controls -->
                <div style="padding:1.25rem;border-top:1px solid rgba(99,102,241,0.1);">

                    <!-- Progress dots -->
                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:1.25rem;">
                        <div class="progress-dot" id="dot-1"></div>
                        <div class="progress-dot" id="dot-2"></div>
                        <div class="progress-dot" id="dot-3"></div>
                        <div class="progress-dot" id="dot-4"></div>
                        <div class="progress-dot" id="dot-5"></div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <button id="btn-start-cam" type="button" class="cam-btn cam-btn-start">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="13" r="4"/><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/></svg>
                            Nyalakan Kamera
                        </button>
                        <button id="btn-capture" disabled type="button" class="cam-btn cam-btn-capture">
                            📸 Jepret (0/5)
                        </button>
                    </div>

                    <!-- Preview thumbnails -->
                    <div style="margin-top:1rem;">
                        <p style="font-size:11px;font-weight:700;color:#475569;letter-spacing:0.05em;text-transform:uppercase;margin-bottom:8px;">Foto Terkumpul</p>
                        <div id="preview-container" style="display:flex;gap:8px;flex-wrap:wrap;min-height:56px;align-items:center;">
                            <p id="no-photos-hint" style="font-size:12px;color:#334155;margin:0;">Belum ada foto dijepret</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Form -->
            <div class="fade-up delay-2" style="background:white;border-radius:24px;border:1px solid #f1f5f9;padding:1.75rem;box-shadow:0 1px 8px rgba(0,0,0,0.04);display:flex;flex-direction:column;gap:1.25rem;">

                <div style="display:flex;align-items:center;gap:10px;margin-bottom:0.25rem;">
                    <div style="width:36px;height:36px;background:#f0f0ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#6366f1" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h2 style="font-size:16px;font-weight:800;color:#1e293b;margin:0;">Data Mahasiswa</h2>
                </div>

                <!-- Step indicator -->
                <div style="background:#f8fafc;border-radius:14px;padding:14px 16px;border:1px solid #e2e8f0;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div id="step-icon" style="width:32px;height:32px;background:#f0f0ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.3s;">
                            <span style="font-size:14px;">📸</span>
                        </div>
                        <div>
                            <p id="step-title" style="font-size:13px;font-weight:700;color:#475569;margin:0;">Langkah 1: Jepret 5 Foto</p>
                            <p id="step-desc" style="font-size:12px;color:#94a3b8;margin:0;">Nyalakan kamera dan ambil 5 foto wajah dari berbagai sudut.</p>
                        </div>
                    </div>
                </div>

                <form id="register-form" action="{{ route('admin.register_face.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
                    @csrf
                    <input type="hidden" name="photos" id="input-photos">

                    <div>
                        <label class="form-label">NIM Mahasiswa</label>
                        <input type="text" name="nim" id="input-nim" placeholder="Masukkan NIM..." disabled required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="input-nama" placeholder="Masukkan nama lengkap..." disabled required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan" id="input-jurusan" disabled required class="form-input">
                            <option value="">-- Pilih Jurusan --</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                        </select>
                    </div>

                    <div style="margin-top:auto;padding-top:0.5rem;border-top:1px solid #f1f5f9;">
                        <button type="submit" id="btn-submit" disabled class="submit-btn">
                            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Ekstrak & Simpan ke AI
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="fade-up delay-3" style="background:white;border-radius:24px;border:1px solid #f1f5f9;box-shadow:0 1px 8px rgba(0,0,0,0.04);overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
                <h2 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Mahasiswa Terdaftar</h2>
                <span style="font-size:12px;color:#94a3b8;background:#f8fafc;padding:4px 12px;border-radius:999px;border:1px solid #e2e8f0;">{{ count($mahasiswa) }} mahasiswa</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">NIM</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Nama Lengkap</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Jurusan</th>
                            <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Status AI</th>
                            <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswa as $mhs)
                        <tr class="table-row" style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:14px 20px;">
                                <span style="font-size:13px;font-weight:700;color:#334155;background:#f8fafc;padding:4px 10px;border-radius:8px;border:1px solid #e2e8f0;">{{ $mhs->nim }}</span>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="font-size:11px;font-weight:700;color:white;">{{ strtoupper(substr($mhs->nama, 0, 1)) }}</span>
                                    </div>
                                    <span style="font-size:14px;font-weight:500;color:#1e293b;">{{ $mhs->nama }}</span>
                                </div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span style="font-size:13px;color:#64748b;">{{ $mhs->jurusan }}</span>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div style="width:7px;height:7px;border-radius:50%;background:#22c55e;"></div>
                                    <span style="font-size:12px;font-weight:600;color:#15803d;">Terdaftar</span>
                                </div>
                            </td>
                            <td style="padding:14px 20px;text-align:center;">
                                <form action="{{ route('admin.mahasiswa.destroy', $mhs->nim) }}" method="POST" onsubmit="return confirm('PERINGATAN!\nYakin ingin menghapus {{ $mhs->nama }}?\nSemua data wajah dan riwayat absen akan HILANG PERMANEN!');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                        Hapus Total
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:3rem;text-align:center;">
                                <p style="font-size:14px;color:#94a3b8;margin:0;">Belum ada mahasiswa yang didaftarkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <canvas id="canvas" style="display:none;"></canvas>

    <style>
    @media (max-width: 768px) { .reg-grid { grid-template-columns: 1fr !important; } }
    </style>

    <script>
        const video = document.getElementById('video-feed');
        const btnStartCam = document.getElementById('btn-start-cam');
        const btnCapture = document.getElementById('btn-capture');
        const previewContainer = document.getElementById('preview-container');
        const noPhotosHint = document.getElementById('no-photos-hint');
        const inputNim = document.getElementById('input-nim');
        const inputNama = document.getElementById('input-nama');
        const inputJurusan = document.getElementById('input-jurusan');
        const btnSubmit = document.getElementById('btn-submit');
        const inputPhotos = document.getElementById('input-photos');
        const placeholder = document.getElementById('camera-placeholder');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const photoCountOverlay = document.getElementById('photo-count-overlay');
        const countText = document.getElementById('count-text');
        const stepTitle = document.getElementById('step-title');
        const stepDesc = document.getElementById('step-desc');
        const stepIcon = document.getElementById('step-icon');

        let capturedPhotos = [];

        const dots = [1,2,3,4,5].map(i => document.getElementById('dot-'+i));
        function updateDots(count) {
            dots.forEach((dot, i) => {
                dot.className = 'progress-dot' + (i < count ? ' done' : (i === count ? ' active' : ''));
            });
        }

        btnStartCam.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.style.display = 'block';
                placeholder.style.display = 'none';
                photoCountOverlay.style.display = 'block';
                btnStartCam.innerHTML = '✓ Kamera Aktif';
                btnStartCam.classList.add('active');
                btnStartCam.disabled = true;
                btnCapture.disabled = false;
                dots[0].classList.add('active');
            } catch(err) {
                alert("Gagal mengakses kamera: " + err.message);
            }
        });

        btnCapture.addEventListener('click', () => {
            if (capturedPhotos.length >= 5) return;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const base64 = canvas.toDataURL('image/jpeg', 0.8);
            capturedPhotos.push(base64);
            if (noPhotosHint) noPhotosHint.style.display = 'none';

            const img = document.createElement('img');
            img.src = base64;
            img.className = 'photo-thumb';
            previewContainer.appendChild(img);

            const count = capturedPhotos.length;
            countText.textContent = count + ' / 5';
            btnCapture.textContent = `📸 Jepret (${count}/5)`;
            updateDots(count);

            if (count === 5) {
                btnCapture.disabled = true;
                btnCapture.textContent = '✅ 5 Foto Terkumpul';
                btnCapture.classList.add('done');
                inputNim.disabled = false; inputNama.disabled = false; inputJurusan.disabled = false;
                btnSubmit.disabled = false; btnSubmit.classList.add('ready');
                inputPhotos.value = JSON.stringify(capturedPhotos);
                stepIcon.innerHTML = '<span style="font-size:14px;">✏️</span>';
                stepTitle.textContent = 'Langkah 2: Isi Data Mahasiswa';
                stepDesc.textContent = '5 foto sudah terkumpul. Isi NIM, nama, dan jurusan lalu tekan simpan.';
                stepTitle.style.color = '#15803d';
            }
        });
    </script>
</body>
</html>