<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Presensi AI | Polibatam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.9)} }
        @keyframes scanLine { 0%{top:0} 50%{top:calc(100% - 2px)} 100%{top:0} }
        @keyframes cornerPulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
        @keyframes blobMove { 0%,100%{border-radius:60% 40% 30% 70%/60% 30% 70% 40%} 50%{border-radius:30% 60% 70% 40%/50% 60% 30% 60%} }

        .fade-up{animation:fadeUp 0.6s ease both;}
        .delay-1{animation-delay:0.1s} .delay-2{animation-delay:0.2s} .delay-3{animation-delay:0.3s}

        .shimmer-text {
            background:linear-gradient(90deg,#60a5fa,#a78bfa,#c084fc,#60a5fa);
            background-size:200% auto; -webkit-background-clip:text;
            -webkit-text-fill-color:transparent; background-clip:text;
            animation:shimmer 3s linear infinite;
        }

        /* Scan overlay corners */
        .corner { position:absolute; width:28px; height:28px; border-color:#818cf8; border-style:solid; animation:cornerPulse 2s ease-in-out infinite; }
        .corner-tl { top:16px; left:16px; border-width:3px 0 0 3px; border-radius:6px 0 0 0; }
        .corner-tr { top:16px; right:16px; border-width:3px 3px 0 0; border-radius:0 6px 0 0; }
        .corner-bl { bottom:16px; left:16px; border-width:0 0 3px 3px; border-radius:0 0 0 6px; }
        .corner-br { bottom:16px; right:16px; border-width:0 3px 3px 0; border-radius:0 0 6px 0; }

        .scan-line {
            position:absolute; left:16px; right:16px; height:2px;
            background:linear-gradient(90deg,transparent,rgba(129,140,248,0.8),transparent);
            animation:scanLine 2.5s ease-in-out infinite;
            box-shadow:0 0 8px rgba(129,140,248,0.6);
        }

        .start-btn {
            width:100%; padding:1rem;
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            color:white; border:none; border-radius:16px;
            font-weight:700; font-size:16px; cursor:pointer;
            box-shadow:0 8px 24px rgba(79,70,229,0.4);
            transition:all 0.3s; position:relative; overflow:hidden;
        }
        .start-btn::before {
            content:''; position:absolute; top:-50%; left:-60%;
            width:40%; height:200%; background:rgba(255,255,255,0.1);
            transform:skewX(-15deg); animation:shimmer 3s ease-in-out infinite;
        }
        .start-btn:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 12px 32px rgba(79,70,229,0.5); }
        .start-btn:disabled { opacity:0.5; cursor:not-allowed; }

        .select-mk {
            width:100%; padding:0.875rem 1rem;
            background:rgba(30,41,59,0.8); border:1.5px solid rgba(99,102,241,0.3);
            border-radius:14px; color:white; font-size:14px;
            outline:none; transition:all 0.25s;
            font-family:'Plus Jakarta Sans',sans-serif;
            appearance:none;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2394a3b8' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:right 1rem center;
        }
        .select-mk:focus { border-color:rgba(99,102,241,0.7); box-shadow:0 0 0 3px rgba(99,102,241,0.15); }
        .select-mk option { background:#1e2d3d; color:white; }

        .status-box { padding:1rem 1.25rem; border-radius:16px; margin-top:1rem; text-align:center; transition:all 0.4s; }
        .status-waiting { background:rgba(30,41,59,0.6); border:1px solid rgba(100,116,139,0.3); }
        .status-challenge { background:rgba(161,98,7,0.15); border:1.5px solid rgba(245,158,11,0.4); }
        .status-passed { background:rgba(6,78,59,0.2); border:1.5px solid rgba(34,197,94,0.4); }
        .status-verifying { background:rgba(49,46,129,0.2); border:1.5px solid rgba(99,102,241,0.4); }
        .status-success { background:rgba(6,78,59,0.2); border:1.5px solid rgba(34,197,94,0.5); }
        .status-error { background:rgba(127,29,29,0.2); border:1.5px solid rgba(239,68,68,0.4); }

        .step-item { display:flex; align-items:flex-start; gap:12px; padding:10px 0; }
        .step-num { width:24px; height:24px; border-radius:50%; background:rgba(99,102,241,0.2); border:1px solid rgba(99,102,241,0.3); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#a5b4fc; flex-shrink:0; }

        .blob { position:absolute; border-radius:60% 40% 30% 70%/60% 30% 70% 40%; animation:blobMove 10s ease-in-out infinite; pointer-events:none; z-index:0; }
    </style>
</head>
<body style="background:#0f172a; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem; position:relative; overflow:hidden;">

    <!-- BG Blobs -->
    <div class="blob" style="width:400px;height:400px;background:radial-gradient(circle,rgba(99,102,241,0.08) 0%,transparent 70%);top:-100px;left:-100px;"></div>
    <div class="blob" style="width:300px;height:300px;background:radial-gradient(circle,rgba(124,58,237,0.06) 0%,transparent 70%);bottom:-80px;right:-80px;animation-delay:-4s;"></div>
    <!-- Grid -->
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(148,163,184,0.05) 1px,transparent 1px);background-size:28px 28px;z-index:0;pointer-events:none;"></div>

    <div style="position:relative;z-index:10;max-width:1100px;width:100%;">

        <!-- Top Brand Bar -->
        <div class="fade-up" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:10px;">
                <img src="https://www.polibatam.ac.id/wp-content/uploads/2022/01/Logo-Polibatam.png" style="width:36px;height:36px;object-fit:contain;" alt="Polibatam">
                <div>
                    <p style="font-size:13px;font-weight:700;color:#e2e8f0;margin:0;">Politeknik Negeri Batam</p>
                    <p style="font-size:11px;color:#64748b;margin:0;">Smart Attendance System</p>
                </div>
            </div>
            <a href="{{ url('/') }}" style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(30,41,59,0.8);border:1px solid rgba(100,116,139,0.3);border-radius:999px;font-size:13px;font-weight:600;color:#94a3b8;text-decoration:none;transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(99,102,241,0.5)';this.style.color='#a5b4fc'" onmouseout="this.style.borderColor='rgba(100,116,139,0.3)';this.style.color='#94a3b8'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Kembali
            </a>
        </div>

        <!-- Main Card -->
        <div class="fade-up delay-1" style="background:rgba(15,23,42,0.95);border:1px solid rgba(99,102,241,0.15);border-radius:28px;overflow:hidden;backdrop-filter:blur(20px);box-shadow:0 20px 60px rgba(0,0,0,0.4);">
            <div style="display:grid;grid-template-columns:1fr 1.6fr;min-height:560px;" class="responsive-grid">

                <!-- LEFT: Controls -->
                <div style="padding:2rem;border-right:1px solid rgba(99,102,241,0.1);display:flex;flex-direction:column;gap:1.5rem;">

                    <!-- Title -->
                    <div>
                        <div style="width:48px;height:48px;background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                            <svg width="24" height="24" fill="none" stroke="#a5b4fc" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M3 21a9 9 0 0118 0"/></svg>
                        </div>
                        <h1 style="font-size:1.6rem;font-weight:800;color:white;margin:0 0 6px;">
                            Presensi <span class="shimmer-text">Face</span>
                        </h1>
                        <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0;">Sistem absensi berbasis pengenalan wajah real-time.</p>
                    </div>

                    <!-- Mata Kuliah Selector -->
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#64748b;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:8px;">Mata Kuliah</label>
                        <div style="position:relative;">
                            <select id="pilih-mk" class="select-mk">
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($matakuliah as $mk)
                                    <option value="{{ $mk->kode_mk }}"
                                        data-hari="{{ strtoupper($mk->hari) }}"
                                        data-mulai="{{ $mk->jam_mulai }}"
                                        data-selesai="{{ $mk->jam_selesai }}"
                                        data-tipe="{{ $mk->tipe_absen }}">
                                        {{ $mk->kode_mk }} — {{ $mk->nama_mk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Start Button -->
                    <button id="btn-start" class="start-btn">
                        <span style="display:flex;align-items:center;justify-content:center;gap:10px;">
                            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/></svg>
                            Mulai Kamera & Absen
                        </span>
                    </button>

                    <!-- Status Box -->
                    <div id="status-container" class="status-box status-waiting" style="display:none;">
                        <p id="status-text" style="font-weight:700;color:#94a3b8;font-size:14px;margin:0;">Menunggu wajah...</p>
                    </div>

                    <!-- Steps guide -->
                    <div style="margin-top:auto;padding-top:1.5rem;border-top:1px solid rgba(100,116,139,0.15);">
                        <p style="font-size:11px;font-weight:700;color:#475569;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:12px;">Panduan</p>
                        <div class="step-item">
                            <div class="step-num">1</div>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Pilih mata kuliah dan klik <strong style="color:#a5b4fc;">Mulai Kamera</strong></p>
                        </div>
                        <div class="step-item">
                            <div class="step-num">2</div>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Tolehkan kepala sesuai instruksi anti-spoofing</p>
                        </div>
                        <div class="step-item">
                            <div class="step-num">3</div>
                            <p style="font-size:13px;color:#64748b;margin:0;line-height:1.5;">Hadap lurus kamera — sistem akan verifikasi otomatis</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Camera -->
                <div style="padding:2rem;display:flex;align-items:center;justify-content:center;background:rgba(8,15,30,0.5);">
                    <div style="width:100%;max-width:560px;">
                        <div id="camera-wrapper" style="position:relative;width:100%;aspect-ratio:16/9;background:rgba(8,15,30,0.9);border-radius:20px;overflow:hidden;border:1.5px solid rgba(99,102,241,0.2);box-shadow:0 0 40px rgba(99,102,241,0.1);">

                            <!-- Placeholder -->
                            <div id="camera-placeholder" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;">
                                <div style="width:72px;height:72px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                    <svg width="32" height="32" fill="none" stroke="#6366f1" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.5;"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </div>
                                <p style="font-size:14px;color:#475569;font-weight:500;">Kamera belum aktif</p>
                                <p style="font-size:12px;color:#334155;">Pilih mata kuliah dan tekan tombol mulai</p>
                            </div>

                            <!-- Scan corners (visible when camera active) -->
                            <div id="scan-overlay" style="display:none;position:absolute;inset:0;pointer-events:none;">
                                <div class="corner corner-tl"></div>
                                <div class="corner corner-tr"></div>
                                <div class="corner corner-bl"></div>
                                <div class="corner corner-br"></div>
                                <div class="scan-line" id="scan-line"></div>
                            </div>

                            <!-- ini Mirror -->
                            <!-- <video id="video" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;" autoplay muted playsinline></video> -->
                            <!-- ini gak Mirror -->
                            <video id="video" 
                                style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none; transform: scaleX(-1);" 
                                autoplay muted playsinline>
                            </video>
                            <canvas id="overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;display:none;"></canvas>
                        </div>

                        <!-- Camera info bar -->
                        <div style="margin-top:12px;display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:rgba(15,23,42,0.8);border-radius:12px;border:1px solid rgba(100,116,139,0.15);">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div id="cam-dot" style="width:8px;height:8px;border-radius:50%;background:#475569;transition:background 0.3s;"></div>
                                <span id="cam-label" style="font-size:12px;color:#475569;font-weight:500;">Kamera tidak aktif</span>
                            </div>
                            <span style="font-size:11px;color:#334155;">Biometric Face Detection</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <canvas id="capture-canvas" style="display:none;"></canvas>

    <style>
    @media (max-width: 768px) {
        .responsive-grid { grid-template-columns: 1fr !important; }
    }
    </style>

<script>
        const video = document.getElementById('video');
        const captureCanvas = document.getElementById('capture-canvas');
        const statusText = document.getElementById('status-text');
        const statusContainer = document.getElementById('status-container');
        const selectMk = document.getElementById('pilih-mk');
        const btnStart = document.getElementById('btn-start');
        const placeholder = document.getElementById('camera-placeholder');
        const scanOverlay = document.getElementById('scan-overlay');
        const camDot = document.getElementById('cam-dot');
        const camLabel = document.getElementById('cam-label');

        let isProcessing = false;
        let livenessPassed = false, currentChallenge = '', livenessState = 'WAITING_CHALLENGE';
        let statusKehadiran = "Hadir";
        let livenessTimer = 0; 
        
        function setStatus(text, type='waiting') {
            statusContainer.style.display = 'block';
            statusText.textContent = text;
            statusContainer.className = 'status-box status-' + type;
            const colors = { waiting:'#94a3b8', challenge:'#fbbf24', passed:'#4ade80', verifying:'#a5b4fc', success:'#4ade80', error:'#f87171' };
            statusText.style.color = colors[type] || '#94a3b8';
        }

        // ==========================================
        // INISIALISASI GOOGLE MEDIAPIPE FACE MESH
        // ==========================================
        const faceMesh = new FaceMesh({locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
        }});

        faceMesh.setOptions({
            maxNumFaces: 1, // Kunci cuma 1 muka (Anti-Joki)
            refineLandmarks: true, // Super akurat untuk bibir dan mata
            minDetectionConfidence: 0.6,
            minTrackingConfidence: 0.6
        });

        faceMesh.onResults(onResults); // Lempar hasil deteksi ke fungsi onResults

        function getBlinkRatio(landmarks) {
            // Jarak Mata Kanan (Horizontal & Vertikal)
            const rightHorizontal = Math.hypot(landmarks[33].x - landmarks[133].x, landmarks[33].y - landmarks[133].y);
            const rightVertical = Math.hypot(landmarks[159].x - landmarks[145].x, landmarks[159].y - landmarks[145].y);
            const rightEAR = rightVertical / rightHorizontal;

            // Jarak Mata Kiri (Horizontal & Vertikal)
            const leftHorizontal = Math.hypot(landmarks[362].x - landmarks[263].x, landmarks[362].y - landmarks[263].y);
            const leftVertical = Math.hypot(landmarks[386].x - landmarks[374].x, landmarks[386].y - landmarks[374].y);
            const leftEAR = leftVertical / leftHorizontal;

            // Kembalikan nilai rata-rata kedua mata
            return (rightEAR + leftEAR) / 2.0;
        }

        function onResults(results) {
            if (isProcessing) return;

            if (results.multiFaceLandmarks && results.multiFaceLandmarks.length > 0) {
                const landmarks = results.multiFaceLandmarks[0];
                
                // Titik Pipi dan Hidung di MediaPipe
                const leftCheek = landmarks[234];
                const rightCheek = landmarks[454];
                const nose = landmarks[1];
                const faceRatio = (nose.x - leftCheek.x) / (rightCheek.x - nose.x);

                // TAHAP 1: TOLEH
                if (livenessState === 'WAITING_CHALLENGE') {
                    if (!currentChallenge) {
                        currentChallenge = Math.random() > 0.5 ? 'KANAN' : 'KIRI';
                        setStatus(`🔒 Anti-Spoofing: Toleh Kepala ke ${currentChallenge}!`, 'challenge');
                        livenessTimer = Date.now(); 
                    }
                    
                    // Ratio MediaPipe sangat presisi
                    if (currentChallenge === 'KANAN' && faceRatio < 0.6) livenessPassed = true;
                    else if (currentChallenge === 'KIRI' && faceRatio > 1.6) livenessPassed = true;
                    
                    if (livenessPassed) {
                        livenessState = 'WAITING_FRONTAL';
                        setStatus('✓ Bagus! Kembali hadap lurus ke kamera...', 'passed');
                    }

                    if (Date.now() - livenessTimer > 5000) {
                        currentChallenge = '';
                        setStatus('Waktu habis! Ulangi tolehan.', 'error');
                    }
                } 
// TAHAP 2: HADAP DEPAN
                else if (livenessState === 'WAITING_FRONTAL') {
                    if (faceRatio > 0.80 && faceRatio < 1.20) {
                        livenessState = 'WAITING_BLINK'; // Ubah state ke Kedip
                        setStatus('👀 Buktikan kamu manusia: Kedipkan Mata!', 'challenge');
                        livenessTimer = Date.now(); 
                    }
                }
                // TAHAP 3: KEDIP MATA (ANTI-SPOOFING FINAL)
                else if (livenessState === 'WAITING_BLINK') {
                    const blinkRatio = getBlinkRatio(landmarks);

                    // Normalnya mata terbuka rasionya ~0.30. 
                    // Kalau kedip/merem, rasionya drop di bawah 0.18.
                    if (blinkRatio < 0.18) {
                        isProcessing = true;
                        livenessState = 'VERIFYING';
                        setStatus('⚡ Memverifikasi identitas...', 'verifying');
                        
                        // Jeda 400ms biar pas dijepret matanya udah melek lagi
                        setTimeout(() => { sendToPython(); }, 400);
                    }

                    // Tetap pertahankan Timer 3 Detik Anti-Hacker
                    if (Date.now() - livenessTimer > 3000) {
                        livenessState = 'WAITING_CHALLENGE';
                        currentChallenge = '';
                        livenessPassed = false;
                        setStatus('Terlalu lama! Ulangi dari awal.', 'error');
                    }
                }
            } else {
                if (livenessState !== 'VERIFYING') {
                    setStatus('Arahkan wajah ke kamera...', 'waiting');
                }
            }
        }

        // ==========================================
        // MULAI KAMERA & API
        // ==========================================
        btnStart.addEventListener('click', async () => {
                    const selectedOption = selectMk.options[selectMk.selectedIndex];
                    if (!selectMk.value) { alert("Harap pilih Mata Kuliah terlebih dahulu!"); return; }

                    // Ambil data dari dropdown
                    const hariMatkul = selectedOption.getAttribute('data-hari');
                    const jamMulai = selectedOption.getAttribute('data-mulai');
                    const jamSelesai = selectedOption.getAttribute('data-selesai');
                    const tipeAbsen = selectedOption.getAttribute('data-tipe');

                    // Cek Waktu & Hari Ini
                    const now = new Date();
                    const namaHari = ['MINGGU','SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'];
                    const hariIni = namaHari[now.getDay()];
                    const waktuSekarang = now.toTimeString().split(' ')[0];

                    // VALIDASI 1: Cek Hari (Ini yang tadi kehapus!)
                    if (hariIni !== hariMatkul) { 
                        alert(`GAGAL: Matkul ini untuk hari ${hariMatkul}, sekarang hari ${hariIni}!`); 
                        return; 
                    }

                    // VALIDASI 2: Cek Jam & Tipe Absen
                    if (tipeAbsen === 'Ketat') {
                        if (waktuSekarang < jamMulai) { alert(`BELUM MULAI: Kelas baru dimulai jam ${jamMulai}.`); return; }
                        if (waktuSekarang > jamSelesai) { alert(`DITOLAK: Batas waktu kelas sudah habis (${jamSelesai}).`); return; }
                        statusKehadiran = "Tepat Waktu";
                    } else {
                        statusKehadiran = (waktuSekarang < jamMulai || waktuSekarang > jamSelesai) ? "Di Luar Jam" : "Tepat Waktu";
                    }

                    // Lanjut Nyalakan Kamera kalau lolos validasi
                    selectMk.disabled = true;
                    btnStart.disabled = true;
                    btnStart.innerHTML = 'Kamera aktif...';
                    placeholder.style.display = 'none';
                    video.style.display = 'block';
                    scanOverlay.style.display = 'block';
                    camDot.style.background = '#22c55e';
                    camLabel.textContent = 'Kamera aktif';
                    setStatus('Memuat AI...', 'waiting');

                    const camera = new Camera(video, {
                        onFrame: async () => {
                            await faceMesh.send({image: video});
                        },
                        width: 1280,
                        height: 720
                    });
                    camera.start();
                });

        async function sendToPython() {
            captureCanvas.width = video.videoWidth;
            captureCanvas.height = video.videoHeight;
            captureCanvas.getContext('2d').drawImage(video, 0, 0);
            const base64Image = captureCanvas.toDataURL('image/jpeg', 0.8);
            
            try {
                const response = await fetch('http://127.0.0.1:5000/verify', {
                    method:'POST',
                    headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({ image:base64Image, kode_mk:selectMk.value, status_kehadiran:statusKehadiran })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    setStatus(`✓ Berhasil: ${result.nama}`, 'success');
                    setTimeout(() => { window.location.href = '/'; }, 2000);
                } else {
                    setStatus(`✗ Gagal: ${result.message}`, 'error');
                    setTimeout(() => {
                        livenessPassed = false; currentChallenge = ''; livenessState = 'WAITING_CHALLENGE'; isProcessing = false;
                    }, 2500);
                }
            } catch(err) {
                setStatus('Server Python tidak aktif!', 'error');
            }
        }
    </script>
</body>
</html>