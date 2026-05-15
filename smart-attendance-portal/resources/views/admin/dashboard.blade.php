<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Presensi | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
        @keyframes countUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

        .fade-up { animation: fadeUp 0.5s ease both; }
        .delay-1{animation-delay:0.08s} .delay-2{animation-delay:0.16s} .delay-3{animation-delay:0.24s} .delay-4{animation-delay:0.32s}

        .stat-card {
            background:white; border-radius:20px; padding:1.5rem;
            border:1px solid #f1f5f9;
            box-shadow:0 1px 8px rgba(0,0,0,0.04);
            transition:all 0.25s;
        }
        .stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.08); }

        .search-input {
            width:100%; padding:0.75rem 1rem 0.75rem 2.75rem;
            background:white; border:1.5px solid #e2e8f0;
            border-radius:999px; font-size:14px; color:#1e293b;
            outline:none; transition:all 0.25s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .search-input:focus { border-color:#6366f1; box-shadow:0 0 0 4px rgba(99,102,241,0.08); }

        .table-row { transition:background 0.15s; }
        .table-row:hover { background:#f8fafc; }

        .badge-hadir { padding:4px 12px; background:#dcfce7; color:#15803d; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #bbf7d0; }
        .badge-telat { padding:4px 12px; background:#fef9c3; color:#854d0e; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #fef08a; }
        .badge-lain { padding:4px 12px; background:#f1f5f9; color:#475569; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #e2e8f0; }

        .live-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:#22c55e; animation:pulse 2s ease-in-out infinite; }

        .delete-btn { padding:7px; background:#fff0f0; color:#ef4444; border:1px solid #fecaca; border-radius:10px; cursor:pointer; transition:all 0.2s; }
        .delete-btn:hover { background:#fee2e2; transform:scale(1.05); }
        .shimmer-text { background:linear-gradient(90deg,#2563eb,#6366f1,#8b5cf6,#2563eb); background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; animation:shimmer 3s linear infinite; }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh;">

    @include('layouts.admin-nav')

    <main style="max-width:1280px; margin:0 auto; padding:2rem 1.5rem;">

        <div class="fade-up flex flex-col md:flex-row" style="margin-bottom:2rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <span class="live-dot"></span>
                    <span style="font-size:12px;font-weight:600;color:#64748b;letter-spacing:0.05em;">REAL-TIME</span>
                </div>
                <h1 style="font-size:1.75rem;font-weight:800;color:#0f172a;margin:0 0 4px;">
                    Log <span class="shimmer-text">Presensi</span> Mahasiswa
                </h1>
                <p style="font-size:14px;color:#94a3b8;margin:0;">Monitoring kehadiran mahasiswa secara real-time.</p>
            </div>

            <div style="position:relative;width:280px;flex-shrink:0;">
                <svg style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Cari NIM atau Nama..." class="search-input">
            </div>
        </div>

        @php
            $logsHariIni = $logs->filter(function($log) {
                return \Carbon\Carbon::parse($log->waktu_masuk)->isToday();
            });
        @endphp

        <div class="fade-up delay-1" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
            <div class="stat-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:13px;font-weight:600;color:#94a3b8;">Total Hadir</span>
                    <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                </div>
                <p style="font-size:2rem;font-weight:800;color:#1e293b;margin:0;">{{ $logsHariIni->count() }}</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">mahasiswa hari ini</p>
            </div>

            <div class="stat-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:13px;font-weight:600;color:#94a3b8;">Tepat Waktu</span>
                    <div style="width:36px;height:36px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                </div>
                <p style="font-size:2rem;font-weight:800;color:#1e293b;margin:0;">{{ $logsHariIni->whereIn('status_kehadiran',['Tepat Waktu','Hadir'])->count() }}</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">hadir tepat waktu</p>
            </div>

            <div class="stat-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:13px;font-weight:600;color:#94a3b8;">Di Luar Jam</span>
                    <div style="width:36px;height:36px;background:#fefce8;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#eab308" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <p style="font-size:2rem;font-weight:800;color:#1e293b;margin:0;">{{ $logsHariIni->whereIn('status_kehadiran',['Di Luar Jam','Telat'])->count() }}</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">di luar jam jadwal</p>
            </div>

            <div class="stat-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:13px;font-weight:600;color:#94a3b8;">Tanggal</span>
                    <div style="width:36px;height:36px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <p style="font-size:1.2rem;font-weight:800;color:#1e293b;margin:0;">{{ \Carbon\Carbon::now()->format('d M') }}</p>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">{{ \Carbon\Carbon::now()->format('Y') }}</p>
            </div>
        </div>

        <div class="fade-up delay-2" style="background:white;border-radius:24px;border:1px solid #f1f5f9;box-shadow:0 1px 8px rgba(0,0,0,0.04);overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,0.15);"></div>
                    <span style="font-size:14px;font-weight:700;color:#1e293b;">Log Kehadiran</span>
                </div>
                <span style="font-size:12px;color:#94a3b8;background:#f8fafc;padding:4px 12px;border-radius:999px;border:1px solid #e2e8f0;">{{ $logs->count() }} entri</span>
            </div>

            <div style="overflow-x:auto;">  
            <div class="fade-up delay-1 flex flex-wrap gap-4 mb-6">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                    <select name="filter_mk" onchange="this.form.submit()" class="search-input" style="width: 200px; padding-left: 1rem;">
                        <option value="">Semua Mata Kuliah</option>
                        @foreach($list_mk as $mk)
                            <option value="{{ $mk->kode_mk }}" {{ request('filter_mk') == $mk->kode_mk ? 'selected' : '' }}>
                                {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">NIM</th>
                            <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Nama</th>
                            <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Mata Kuliah</th>
                            <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Waktu Masuk</th>
                            <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Status</th>
                            <th style="padding:14px 20px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Bukti</th>
                            <th style="padding:14px 20px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.08em;text-transform:uppercase;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="table-row" style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:14px 20px;">
                                <span style="font-size:13px;font-weight:700;color:#334155;background:#f8fafc;padding:4px 10px;border-radius:8px;border:1px solid #e2e8f0;">{{ $log->nim }}</span>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="font-size:11px;font-weight:700;color:white;">{{ strtoupper(substr($log->nama ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <span style="font-size:14px;color:#1e293b;font-weight:500;">{{ $log->nama ?? 'Tidak Diketahui' }}</span>
                                </div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span style="font-size:13px;font-weight:600;color:#6366f1;background:#f5f3ff;padding:4px 10px;border-radius:8px;">{{ $log->kode_mk }}</span>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="font-size:13px;color:#475569;">
                                    <span style="font-weight:600;color:#1e293b;">{{ \Carbon\Carbon::parse($log->waktu_masuk)->format('d M Y') }}</span>
                                    <br>
                                    <span style="font-size:12px;color:#94a3b8;">{{ \Carbon\Carbon::parse($log->waktu_masuk)->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td style="padding:14px 20px;">
                                @if($log->status_kehadiran == 'Tepat Waktu' || $log->status_kehadiran == 'Hadir')
                                    <span class="badge-hadir">✓ Tepat Waktu</span>
                                @elseif($log->status_kehadiran == 'Di Luar Jam' || $log->status_kehadiran == 'Telat')
                                    <span class="badge-telat" title="Absen diterima, namun di luar jam jadwal">⏰ Di Luar Jam</span>
                                @else
                                    <span class="badge-lain">{{ $log->status_kehadiran ?? 'Hadir' }}</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px;text-align:center;">
                                @if(file_exists(public_path('presensi_log/' . $log->bukti_liveness)))
                                    <a href="/presensi_log/{{ $log->bukti_liveness }}" target="_blank" style="display:inline-block;border-radius:10px;overflow:hidden;border:2px solid #e2e8f0;transition:all 0.2s;" onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='#e2e8f0'">
                                        <img src="/presensi_log/{{ $log->bukti_liveness }}" alt="bukti" style="width:40px;height:40px;object-fit:cover;display:block;filter:grayscale(1);transition:filter 0.2s;" onmouseover="this.style.filter='grayscale(0)'" onmouseout="this.style.filter='grayscale(1)'">
                                    </a>
                                @else
                                    <div style="width:40px;height:40px;background:#f8fafc;border:1px dashed #e2e8f0;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;margin:0 auto;" title="Foto kadaluarsa">
                                        <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                    </div>
                                @endif
                            </td>
                            <td style="padding:14px 20px;text-align:center;">
                                <form action="{{ route('admin.presensi.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus log absen ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" title="Hapus Log">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding:4rem;text-align:center;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                    <div style="width:64px;height:64px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                        <svg width="28" height="28" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                    </div>
                                    <p style="font-size:15px;font-weight:600;color:#475569;margin:0;">Belum ada data presensi hari ini</p>
                                    <p style="font-size:13px;color:#94a3b8;margin:0;">Log kehadiran akan muncul setelah mahasiswa melakukan absensi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <form action="{{ route('admin.presensi.purge') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data lama?')">
                @csrf
                <div class="flex items-center gap-2">
                    <select name="range" class="search-input" style="width: 200px; padding-left: 1rem; border-color: #fca5a5;">
                        <option value="7_days">Hapus > 7 Hari Lalu</option>
                        <option value="30_days">Hapus > 30 Hari Lalu</option>
                        <option value="all">Hapus Semua Data</option>
                    </select>
                    <button type="submit" class="delete-btn" style="padding: 10px 15px; background: #ef4444; color: white;">
                        Eksekusi Hapus
                    </button>
                </div>
            </form>
        </div>

        <div style="padding: 1.5rem; border-top: 1px solid #f1f5f9; background: #f8fafc;">
            {{ $logs->appends(request()->input())->links() }}
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        if(row.cells.length > 1) {
                            let nim = row.cells[0].textContent.toLowerCase();
                            let nama = row.cells[1].textContent.toLowerCase();
                            row.style.display = (nim.includes(filter) || nama.includes(filter)) ? '' : 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>