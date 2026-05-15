<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mata Kuliah | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        .fade-up{animation:fadeUp 0.5s ease both;}
        .delay-1{animation-delay:0.08s} .delay-2{animation-delay:0.16s}
        .shimmer-text { background:linear-gradient(90deg,#2563eb,#6366f1,#8b5cf6,#2563eb); background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; animation:shimmer 3s linear infinite; }
        .form-input {
            width:100%; padding:0.75rem 1rem; background:#f8fafc;
            border:1.5px solid #e2e8f0; border-radius:12px;
            font-size:14px; color:#1e293b; outline:none;
            transition:all 0.25s; font-family:'Plus Jakarta Sans',sans-serif;
            box-sizing:border-box;
        }
        .form-input:focus { background:#fff; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.08); }
        .form-input.edit-mode { border-color:#f59e0b; background:#fffbeb; }
        .form-label { display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:6px; letter-spacing:0.03em; }
        .submit-btn {
            width:100%; padding:0.875rem;
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            color:white; border:none; border-radius:12px;
            font-weight:700; font-size:15px; cursor:pointer;
            transition:all 0.3s; box-shadow:0 4px 16px rgba(79,70,229,0.25);
        }
        .submit-btn:hover { transform:translateY(-1px); box-shadow:0 8px 24px rgba(79,70,229,0.35); }
        .table-row { transition:background 0.15s; }
        .table-row:hover { background:#f8fafc; }
        .badge-ketat { padding:3px 10px; background:#fef2f2; color:#dc2626; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #fecaca; }
        .badge-fleksibel { padding:3px 10px; background:#f0fdf4; color:#16a34a; border-radius:999px; font-size:11px; font-weight:700; border:1px solid #bbf7d0; }
        .delete-btn { padding:7px; background:#fff0f0; color:#ef4444; border:1px solid #fecaca; border-radius:9px; cursor:pointer; transition:all 0.2s; }
        .delete-btn:hover { background:#fee2e2; transform:scale(1.05); }
    </style>
</head>
<body style="background:#f8fafc; min-height:100vh;">

    @include('layouts.admin-nav')

    <main style="max-width:1200px; margin:0 auto; padding:2rem 1.5rem;">

        <!-- Header -->
        <div class="fade-up" style="margin-bottom:2rem;">
            <h1 style="font-size:1.75rem;font-weight:800;color:#0f172a;margin:0 0 4px;">
                Kelola <span class="shimmer-text">Mata Kuliah</span>
            </h1>
            <p style="font-size:14px;color:#94a3b8;margin:0;">Tambah, edit, dan hapus mata kuliah untuk sistem presensi.</p>
        </div>

        @if(session('success'))
        <div class="fade-up" style="margin-bottom:1.5rem;padding:1rem 1.25rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;display:flex;align-items:center;gap:10px;">
            <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size:14px;font-weight:600;color:#15803d;">{{ session('success') }}</span>
        </div>
        @endif

        <div style="display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start;" class="main-grid">

            <!-- Form Card -->
            <div class="fade-up delay-1" style="background:white;border-radius:24px;border:1px solid #f1f5f9;padding:1.75rem;box-shadow:0 1px 8px rgba(0,0,0,0.04);position:sticky;top:88px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;">
                    <div style="width:36px;height:36px;background:#f0f0ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" stroke="#6366f1" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    </div>
                    <h2 style="font-size:16px;font-weight:800;color:#1e293b;margin:0;">Tambah / Edit Matkul</h2>
                </div>

                <form action="{{ route('admin.matakuliah.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
                    @csrf

                    <div>
                        <label class="form-label">Kode MK</label>
                        <input type="text" name="kode_mk" id="input-kode-mk" required placeholder="Mis: TI101" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" required placeholder="Nama lengkap matkul" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Hari</label>
                        <select name="hari" required class="form-input">
                            <option value="SENIN">Senin</option><option value="SELASA">Selasa</option>
                            <option value="RABU">Rabu</option><option value="KAMIS">Kamis</option>
                            <option value="JUMAT">Jumat</option><option value="SABTU">Sabtu</option>
                            <option value="MINGGU">Minggu</option>
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" required class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Tipe Absen</label>
                        <select name="tipe_absen" required class="form-input">
                            <option value="Fleksibel">Fleksibel – Bebas jam, asal hari sama</option>
                            <option value="Ketat">Ketat – Wajib antara jam mulai & selesai</option>
                        </select>
                    </div>

                    <!-- Edit mode hint -->
                    <div id="edit-hint" style="display:none;padding:10px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;">
                        <p style="font-size:12px;font-weight:600;color:#92400e;margin:0;">✏️ Mode Edit – data yang ada akan diperbarui</p>
                    </div>

                    <button type="submit" class="submit-btn" style="margin-top:4px;">Simpan Mata Kuliah</button>
                </form>

                <!-- Tips -->
                <div style="margin-top:1.25rem;padding:12px 14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;">
                    <p style="font-size:12px;color:#0369a1;font-weight:500;margin:0;line-height:1.5;">
                        💡 <strong>Tips:</strong> Ketik Kode MK yang sudah ada untuk masuk ke mode edit otomatis.
                    </p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="fade-up delay-2" style="background:white;border-radius:24px;border:1px solid #f1f5f9;box-shadow:0 1px 8px rgba(0,0,0,0.04);overflow:hidden;">
                <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
                    <h2 style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">Daftar Mata Kuliah</h2>
                    <span style="font-size:12px;color:#94a3b8;background:#f8fafc;padding:4px 12px;border-radius:999px;border:1px solid #e2e8f0;">{{ count($matakuliah) }} matkul</span>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                                <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Kode & Nama</th>
                                <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Jadwal</th>
                                <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Tipe</th>
                                <th style="padding:12px 20px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:0.06em;text-transform:uppercase;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matakuliah as $mk)
                            <tr class="table-row" style="border-bottom:1px solid #f8fafc;">
                                <td style="padding:14px 20px;">
                                    <span style="display:inline-block;padding:3px 10px;background:#f0f0ff;color:#6366f1;border-radius:8px;font-size:12px;font-weight:700;margin-bottom:4px;">{{ $mk->kode_mk }}</span>
                                    <p style="font-size:14px;font-weight:500;color:#1e293b;margin:0;">{{ $mk->nama_mk }}</p>
                                </td>
                                <td style="padding:14px 20px;">
                                    <p style="font-size:13px;font-weight:600;color:#334155;margin:0 0 2px;">{{ ucfirst(strtolower($mk->hari)) }}</p>
                                    <p style="font-size:12px;color:#94a3b8;margin:0;">{{ $mk->jam_mulai }} – {{ $mk->jam_selesai }}</p>
                                </td>
                                <td style="padding:14px 20px;">
                                    @if($mk->tipe_absen == 'Ketat')
                                        <span class="badge-ketat">Ketat</span>
                                    @else
                                        <span class="badge-fleksibel">Fleksibel</span>
                                    @endif
                                </td>
                                <td style="padding:14px 20px;text-align:center;">
                                    <form action="{{ route('admin.matakuliah.destroy', $mk->kode_mk) }}" method="POST" onsubmit="return confirm('Menghapus matkul AKAN MENGHAPUS SEMUA DATA ABSEN terkait. Lanjutkan?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn" title="Hapus Matkul">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if(count($matakuliah) === 0)
                            <tr>
                                <td colspan="4" style="padding:3rem;text-align:center;">
                                    <p style="font-size:14px;color:#94a3b8;margin:0;">Belum ada mata kuliah. Tambahkan melalui form di samping.</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <style>
    @media (max-width: 768px) { .main-grid { grid-template-columns: 1fr !important; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputKode = document.getElementById('input-kode-mk');
            const editHint = document.getElementById('edit-hint');
            inputKode.addEventListener('blur', async function() {
                const kode = this.value.trim();
                if (!kode) return;
                try {
                    const response = await fetch(`/admin/matakuliah/api/${kode}`);
                    const data = await response.json();
                    if (data) {
                        document.querySelector('input[name="nama_mk"]').value = data.nama_mk;
                        document.querySelector('select[name="hari"]').value = data.hari;
                        document.querySelector('input[name="jam_mulai"]').value = data.jam_mulai;
                        document.querySelector('input[name="jam_selesai"]').value = data.jam_selesai;
                        document.querySelector('select[name="tipe_absen"]').value = data.tipe_absen;
                        inputKode.classList.add('edit-mode');
                        editHint.style.display = 'block';
                    } else {
                        document.querySelector('input[name="nama_mk"]').value = '';
                        inputKode.classList.remove('edit-mode');
                        editHint.style.display = 'none';
                    }
                } catch(e) { console.log("Matkul baru."); }
            });
        });
    </script>
</body>
</html>