<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Smart Presensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes blobMove { 0%,100%{border-radius:60% 40% 30% 70%/60% 30% 70% 40%} 50%{border-radius:30% 60% 70% 40%/50% 60% 30% 60%} }
        @keyframes spinSlow { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.6;transform:scale(0.95)} }

        .fade-up { animation: fadeUp 0.6s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        .blob {
            position:absolute; border-radius:60% 40% 30% 70%/60% 30% 70% 40%;
            animation: blobMove 10s ease-in-out infinite, spinSlow 25s linear infinite;
            pointer-events:none; z-index:0;
        }
        .shimmer-text {
            background: linear-gradient(90deg, #2563eb, #6366f1, #8b5cf6, #2563eb);
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }
        .input-field {
            width:100%; padding:0.875rem 1rem 0.875rem 3rem;
            background:#f8fafc; border:1.5px solid #e2e8f0;
            border-radius:14px; font-size:15px; color:#1e293b;
            outline:none; transition:all 0.25s;
        }
        .input-field:focus { background:#fff; border-color:#6366f1; box-shadow:0 0 0 4px rgba(99,102,241,0.08); }
        .input-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#94a3b8; }
        .login-btn {
            width:100%; padding:1rem;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color:white; border:none; border-radius:14px;
            font-weight:700; font-size:16px; cursor:pointer;
            box-shadow:0 8px 24px rgba(79,70,229,0.3);
            transition:all 0.3s; position:relative; overflow:hidden;
        }
        .login-btn::before {
            content:''; position:absolute; top:-50%; left:-60%;
            width:40%; height:200%; background:rgba(255,255,255,0.15);
            transform:skewX(-15deg);
            animation: shimmer 3s ease-in-out infinite;
        }
        .login-btn:hover { transform:translateY(-2px); box-shadow:0 12px 32px rgba(79,70,229,0.42); }
        .login-btn:active { transform:translateY(0); }

        .logo-ring {
            position:absolute; inset:-10px; border-radius:50%;
            border: 2px dashed rgba(99,102,241,0.2);
            animation: spinSlow 15s linear infinite;
        }
        .logo-ring-2 {
            position:absolute; inset:-22px; border-radius:50%;
            border: 1.5px dashed rgba(59,130,246,0.12);
            animation: spinSlow 22s linear infinite reverse;
        }
        .status-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:#22c55e; animation: pulse 2s ease-in-out infinite; }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; position:relative; overflow:hidden;">

    <!-- Background blobs -->
    <div class="blob" style="width:450px;height:450px;background:radial-gradient(circle,rgba(99,102,241,0.12) 0%,transparent 70%);top:-100px;left:-120px;"></div>
    <div class="blob" style="width:350px;height:350px;background:radial-gradient(circle,rgba(59,130,246,0.1) 0%,transparent 70%);bottom:-80px;right:-80px;animation-delay:-5s;"></div>

    <!-- Grid dots pattern -->
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle, rgba(148,163,184,0.15) 1px, transparent 1px);background-size:32px 32px;z-index:0;pointer-events:none;"></div>

    <div style="position:relative; z-index:10; width:100%; max-width:420px;">

        <!-- Logo & Brand -->
        <div class="fade-up" style="text-align:center; margin-bottom:2rem;">
            <div style="display:flex; justify-content:center; margin-bottom:1.25rem;">
                <div style="position:relative; display:inline-flex;">
                    <div class="logo-ring-2"></div>
                    <div class="logo-ring"></div>
                    <div style="width:80px;height:80px;background:white;border-radius:50%;box-shadow:0 8px 32px rgba(99,102,241,0.2);border:1px solid rgba(148,163,184,0.2);display:flex;align-items:center;justify-content:center;">
                        <img src="https://www.polibatam.ac.id/wp-content/uploads/2022/01/Logo-Polibatam.png" style="width:56px;height:56px;object-fit:contain;" alt="Polibatam">
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:8px;">
                <span class="status-dot"></span>
                <span style="font-size:12px;font-weight:600;color:#64748b;letter-spacing:0.05em;">SISTEM AKTIF</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#0f172a;margin-bottom:4px;">
                Admin <span class="shimmer-text">Panel</span>
            </h1>
            <p style="font-size:14px;color:#94a3b8;">Silakan masuk untuk mengelola presensi.</p>
        </div>

        <!-- Card -->
        <div class="fade-up delay-1" style="background:white;border-radius:24px;border:1px solid rgba(226,232,240,0.8);padding:2rem;box-shadow:0 4px 24px rgba(0,0,0,0.06);">

            @if(session('error'))
            <div style="margin-bottom:1.25rem;padding:0.875rem 1rem;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;display:flex;align-items:center;gap:10px;">
                <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                <span style="font-size:14px;font-weight:600;color:#dc2626;">{{ session('error') }}</span>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" style="display:flex;flex-direction:column;gap:1.125rem;">
                @csrf

                <!-- Username -->
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#475569;margin-bottom:6px;">Username</label>
                    <div style="position:relative;">
                        <span class="input-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text" name="username" placeholder="Masukkan username..." required class="input-field">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#475569;margin-bottom:6px;">Password</label>
                    <div style="position:relative;">
                        <span class="input-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </span>
                        <input type="password" name="password" placeholder="Masukkan password..." required class="input-field" id="pwd-input">
                        <button type="button" onclick="togglePwd()" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
                            <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn" style="margin-top:0.5rem;">
                    Masuk Sistem
                    <svg style="display:inline;margin-left:8px;vertical-align:middle;" width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
            <div style="text-align:center; margin-top:1.5rem;">
                <a href="{{ url('/') }}" style="font-size:14px; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-weight:600; transition:all 0.2s;" onmouseover="this.style.color='#4f46e5'; this.style.transform='translateX(-3px)';" onmouseout="this.style.color='#64748b'; this.style.transform='translateX(0)';">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        
        

        <!-- Footer -->
        <p class="fade-up delay-2" style="text-align:center;margin-top:1.5rem;font-size:12px;color:#cbd5e1;">
            &copy; {{ date('Y') }} Smart Presensi · Politeknik Negeri Batam
        </p>
    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('pwd-input');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>