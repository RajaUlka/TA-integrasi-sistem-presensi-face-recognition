@extends('layouts.app')

@section('styles')
<style>
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
    @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
    @keyframes spinSlow { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
    @keyframes blobMove { 0%,100%{border-radius:60% 40% 30% 70%/60% 30% 70% 40%} 50%{border-radius:30% 60% 70% 40%/50% 60% 30% 60%} }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    .float-anim { animation: float 4s ease-in-out infinite; }
    .fade-up-1 { animation: fadeUp 0.7s ease forwards; }
    .fade-up-2 { animation: fadeUp 0.7s 0.15s ease both; }
    .fade-up-3 { animation: fadeUp 0.7s 0.3s ease both; }
    .fade-up-4 { animation: fadeUp 0.7s 0.45s ease both; }

    .shimmer-text {
        background: linear-gradient(90deg, #2563eb, #6366f1, #8b5cf6, #2563eb);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 3s linear infinite;
    }
    .blob-1 {
        position:absolute; width:500px; height:500px;
        background:radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
        animation: blobMove 8s ease-in-out infinite, spinSlow 20s linear infinite;
        top:-80px; left:-100px; z-index:0; pointer-events:none;
    }
    .blob-2 {
        position:absolute; width:400px; height:400px;
        background:radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
        animation: blobMove 10s ease-in-out infinite reverse;
        bottom:-60px; right:-60px; z-index:0; pointer-events:none;
    }
    .glass-btn {
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(148,163,184,0.3);
        transition: all 0.25s;
    }
    .glass-btn:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform:translateY(-1px); }
    .scan-btn {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
        position: relative; overflow: hidden;
        transition: all 0.3s;
    }
    .scan-btn::before {
        content:''; position:absolute; top:-50%; left:-60%;
        width:40%; height:200%; background:rgba(255,255,255,0.15);
        transform:skewX(-15deg);
        animation: shimmer 3s ease-in-out infinite;
    }
    .scan-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(79,70,229,0.45) !important; }
    .logo-ring {
        position:absolute; inset:-8px; border-radius:50%;
        border: 2px dashed rgba(99,102,241,0.25);
        animation: spinSlow 12s linear infinite;
    }
    .logo-ring-2 {
        position:absolute; inset:-18px; border-radius:50%;
        border: 1.5px dashed rgba(59,130,246,0.15);
        animation: spinSlow 20s linear infinite reverse;
    }
    .status-dot { animation: pulse 2s ease-in-out infinite; }
</style>
@endsection

@section('content')
<div class="blob-1"></div>
<div class="blob-2"></div>

<!-- Navbar -->
<nav style="position:relative; z-index:50; padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center;">
    <div class="fade-up-1" style="display:flex; align-items:center; gap:10px;">
        <img src="https://www.polibatam.ac.id/wp-content/uploads/2022/01/Logo-Polibatam.png"
             style="height:36px; width:auto;" alt="Logo Polibatam">
        <span style="font-size:14px; font-weight:600; color:#475569;">Politeknik Negeri Batam</span>
    </div>
    <a href="{{ route('login') }}" class="glass-btn fade-up-1"
       style="display:flex; align-items:center; gap:8px; padding:0.6rem 1.25rem; border-radius:999px; font-weight:600; font-size:14px; color:#334155; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
        </svg>
        Login Admin
    </a>
</nav>

<!-- Main -->
<main style="position:relative; z-index:10; min-height:calc(100vh - 140px); display:flex; align-items:center; justify-content:center; padding:2rem;">
    <div style="max-width:760px; width:100%; text-align:center;">

        <!-- Logo Float -->
        <div class="fade-up-1" style="display:flex; justify-content:center; margin-bottom:2.5rem;">
            <div class="float-anim" style="position:relative; display:inline-flex;">
                <div class="logo-ring-2"></div>
                <div class="logo-ring"></div>
                <div style="background:white; padding:1.25rem; border-radius:50%; box-shadow:0 8px 32px rgba(99,102,241,0.18); border:1px solid rgba(148,163,184,0.2); position:relative;">
                    <img src="https://www.polibatam.ac.id/wp-content/uploads/2022/01/Logo-Polibatam.png"
                         style="width:72px; height:72px; object-fit:contain;" alt="Polibatam">
                </div>
            </div>
        </div>

        <!-- Badge -->
        <div class="fade-up-2" style="display:flex; justify-content:center; margin-bottom:1.5rem;">
            <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 14px; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.2); border-radius:999px; font-size:12px; font-weight:600; color:#6366f1; letter-spacing:0.05em;">
                <span class="status-dot" style="width:6px;height:6px;border-radius:50%;background:#6366f1;display:inline-block;"></span>
                SISTEM PRESENSI AKTIF
            </span>
        </div>

        <!-- Heading -->
        <h1 class="fade-up-2" style="font-size:clamp(2.4rem, 7vw, 4.5rem); font-weight:800; line-height:1.1; color:#0f172a; margin-bottom:1.25rem;">
            Sistem <span class="shimmer-text">Presensi Wajah</span>
        </h1>
        <p class="fade-up-3" style="font-size:1.1rem; color:#64748b; max-width:520px; margin:0 auto 2.5rem; line-height:1.75;">
            Absensi cerdas berbasis pengenalan wajah untuk mahasiswa Politeknik Negeri Batam. Cepat, akurat, tanpa sentuh.
        </p>

        <!-- CTA -->
        <div class="fade-up-4" style="display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
            <a href="{{ route('scan') }}" class="scan-btn"
               style="display:inline-flex; align-items:center; gap:12px; padding:1rem 2.5rem; border-radius:16px; color:white; font-weight:700; font-size:1.1rem; text-decoration:none; box-shadow:0 8px 24px rgba(79,70,229,0.35);">
                <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/>
                </svg>
                Mulai Presensi
                <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <p style="font-size:12px; color:#94a3b8;">Arahkan wajah ke kamera untuk memulai</p>
        </div>

        <!-- Stats -->
        <div class="fade-up-4" style="margin-top:3.5rem; display:flex; justify-content:center; gap:2.5rem; flex-wrap:wrap; align-items:center;">
            <div style="text-align:center;">
                <p style="font-size:1.4rem; font-weight:800; color:#1e293b; margin:0;">Smart</p>
                <p style="font-size:12px; color:#94a3b8; margin:2px 0 0;">Verification</p>
            </div>
            <div style="width:1px; height:32px; background:#e2e8f0;"></div>
            <div style="text-align:center;">
                <p style="font-size:1.4rem; font-weight:800; color:#1e293b; margin:0;">Real-time</p>
                <p style="font-size:12px; color:#94a3b8; margin:2px 0 0;">Deteksi Wajah</p>
            </div>
            <div style="width:1px; height:32px; background:#e2e8f0;"></div>
            <div style="text-align:center;">
                <p style="font-size:1.4rem; font-weight:800; color:#1e293b; margin:0;">Auto</p>
                <p style="font-size:12px; color:#94a3b8; margin:2px 0 0;">Rekap Hadir</p>
            </div>
        </div>

    </div>
</main>
@endsection