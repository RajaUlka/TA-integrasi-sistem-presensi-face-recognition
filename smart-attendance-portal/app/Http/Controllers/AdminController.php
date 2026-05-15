<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Taruh di atas sendiri

class AdminController extends Controller
{
    // Ganti fungsi dashboard lama dengan ini
    public function dashboard(Request $request)
    {
        $query = DB::table('presensi')
            ->leftJoin('mahasiswa', 'presensi.nim', '=', 'mahasiswa.nim')
            ->select('presensi.*', 'mahasiswa.nama');

        // Fitur Filter Mata Kuliah
        if ($request->has('filter_mk') && $request->filter_mk != '') {
            $query->where('presensi.kode_mk', $request->filter_mk);
        }

        // Gunakan paginate(10) untuk sistem halaman (10 data per halaman)
        $logs = $query->orderBy('presensi.waktu_masuk', 'desc')->paginate(10);
        
        // Ambil daftar matkul untuk dropdown filter
        $list_mk = DB::table('matakuliah')->select('kode_mk', 'nama_mk')->get();

        return view('admin.dashboard', compact('logs', 'list_mk'));
    }

    // Tambahkan fungsi baru untuk hapus massal (Purge)
    public function purgePresensi(Request $request)
    {
        $request->validate(['range' => 'required']);
        
        try {
            $query = DB::table('presensi');
            $range = $request->range;

            if ($range == '7_days') {
                $query->where('waktu_masuk', '<', now()->subDays(7));
                $msg = "Data lebih dari 7 hari berhasil dibersihkan!";
            } elseif ($range == '30_days') {
                $query->where('waktu_masuk', '<', now()->subDays(30));
                $msg = "Data lebih dari 1 bulan berhasil dibersihkan!";
            } elseif ($range == 'all') {
                $query->truncate(); // Hapus semua isi tabel
                $msg = "Seluruh log presensi berhasil dikosongkan!";
            }

            $query->delete();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }

    // Halaman 2: Pendaftaran Wajah
    public function registerFace()
    {
        // Ambil data mahasiswa untuk ditampilkan di dropdown form
        $mahasiswa = DB::table('mahasiswa')->get();
        
        return view('admin.register-face', compact('mahasiswa'));
    }
    public function storeFace(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:15',
            'nama' => 'required|string|max:100',
            'jurusan' => 'required|string|max:50',
            'photos' => 'required|string' // Array JSON dari 5 foto base64
        ]);

        try {
            // 1. Simpan atau Update Data ke Tabel Mahasiswa
            DB::table('mahasiswa')->updateOrInsert(
                ['nim' => $request->nim],
                [
                    'nama' => $request->nama,
                    'jurusan' => $request->jurusan
                ]
            );

            // 2. Kirim 5 foto ke Python API untuk diekstrak jadi vektor
            $photos = json_decode($request->photos);
            
            $response = Http::timeout(60)->post('http://127.0.0.1:5000/register', [
                'nim' => $request->nim,
                'photos' => $photos
            ]);

            if ($response->successful() && $response->json('status') == 'success') {
                return redirect()->route('admin.register_face')->with('success', 'Wajah berhasil didaftarkan ke AI!');
            }

            return back()->with('error', 'AI Gagal mengekstrak wajah: ' . $response->json('message'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

        // Halaman 3: Kelola Mata Kuliah
        public function matakuliah()
        {
            $matakuliah = DB::table('matakuliah')->get();
            return view('admin.matakuliah', compact('matakuliah'));
        }

        public function storeMatakuliah(Request $request)
        {
            // Pastikan kolom validasi disesuaikan dengan input form yang baru
            $request->validate([
                'kode_mk' => 'required',
                'nama_mk' => 'required',
                'hari' => 'required',
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'tipe_absen' => 'required',
            ]);
    
            try {
                DB::table('matakuliah')->updateOrInsert(
                   ['kode_mk' => $request->kode_mk],
                   [
                       'nama_mk' => $request->nama_mk,
                       'hari' => $request->hari,
                       'jam_mulai' => $request->jam_mulai,
                       'jam_selesai' => $request->jam_selesai,
                       'tipe_absen' => $request->tipe_absen
                   ]
                );
                return back()->with('success', 'Mata Kuliah berhasil disimpan!');
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Halaman 4: Hapus Mahasiswa & Seluruh Datanya
    public function destroyMahasiswa($nim)
    {
        try {
            // 1. Hapus SEMUA data absen mahasiswa ini (Anak)
            DB::table('presensi')->where('nim', $nim)->delete();
            
            // 2. Hapus data vektor wajah mahasiswa ini (Anak)
            DB::table('wajah_features')->where('nim', $nim)->delete();
            
            // 3. Terakhir, baru hapus data mahasiswanya (Induk)
            DB::table('mahasiswa')->where('nim', $nim)->delete();

            return back()->with('success', "Data Mahasiswa NIM $nim beserta seluruh riwayat presensi & wajah berhasil dihapus permanen!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    // Halaman 1: Hapus Log Presensi
    public function destroyPresensi($id)
    {
        try {
            DB::table('presensi')->where('id', $id)->delete();
            return back()->with('success', 'Log presensi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }

    // Halaman 3: Hapus Mata Kuliah
    public function destroyMatakuliah($kode)
    {
        try {
            // Hapus log presensi yang memakai matkul ini dulu (Constraint)
            DB::table('presensi')->where('kode_mk', $kode)->delete();
            // Baru hapus matkulnya
            DB::table('matakuliah')->where('kode_mk', $kode)->delete();
            
            return back()->with('success', "Mata Kuliah $kode beserta data presensinya berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus matkul: ' . $e->getMessage());
        }
    }
    // Fungsi API untuk Auto-fill
    public function getMatakuliahApi($kode)
    {
        $mk = DB::table('matakuliah')->where('kode_mk', $kode)->first();
        return response()->json($mk);
    }
}