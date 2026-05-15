<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Presensi AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .foto-bukti {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Smart Attendance System - Face Recognition</span>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Daftar Kehadiran Mahasiswa (Real-time dari Python)</h5>
        </div>
        <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Nama / NIM</th> <!-- Kita update headernya -->
                        <th>Mata Kuliah</th>
                        <th>Bukti Liveness</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_presensi as $p)
                    <tr>
                        <td>{{ $p->waktu_masuk }}</td>
                        <td>
                            <!-- Memanggil Relasi Nama Mahasiswa -->
                            <strong>{{ $p->mahasiswa->nama ?? 'Data Mahasiswa Hilang' }}</strong> <br>
                            <small class="text-muted">{{ $p->nim }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $p->kode_mk }}</span></td>
                        <td>
                            <img src="{{ asset('presensi_log/' . $p->bukti_liveness) }}" 
                                class="foto-bukti" 
                                onerror="this.src='https://via.placeholder.com/100?text=No+Photo'">
                        </td>
                        <td><span class="badge bg-success">Verified</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>