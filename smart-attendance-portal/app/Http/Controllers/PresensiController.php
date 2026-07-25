<?php

namespace App\Http\Controllers;

use App\Models\Presensi; // Import Model Presensi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PresensiController extends Controller
{
    public function index()
    {
        // Ambil data presensi terbaru, urutkan dari yang paling baru
        // Kita pakai 'with' nanti kalau mau relasi ke Mahasiswa
        $data_presensi = Presensi::orderBy('waktu_masuk', 'desc')->get();

        return view('index', compact('data_presensi'));
    }

    public function jalankanPython()
    {
        // 1. Tentukan folder di mana script Python kamu berada
        $folderPath = 'C:\xampp\htdocs\File TA\engine-ai';
        $scriptName = 'presensi.py';
    

        $query = "start /d \"$folderPath\" python $scriptName";
    
        try {
            pclose(popen($query, "r"));
            return redirect()->back()->with('success', 'Kamera sedang dinyalakan...');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyalakan kamera: ' . $e->getMessage());
        }
    }
    public function scan()
    {
        // Ambil data matkul biar bisa dipilih user sebelum absen
        $matakuliah = DB::table('matakuliah')->get();
        return view('presensi.scan', compact('matakuliah'));
    }

        public function kirimDataKeMoodle($nim, $status, $sessionId)
    {
        // URL REST API Moodle lokal kamu
        $moodleUrl = 'http://localhost/moodle/webservice/rest/server.php';
        
        // Token ini nanti kita ambil dari Moodle setelah setup besok pagi
        $token = 'MASUKKAN_TOKEN_MOODLE_KAMU_DISINI'; 
        
        try {
            // Moodle REST API mewajibkan format x-www-form-urlencoded (asForm)
            $response = Http::asForm()->post($moodleUrl, [
                'wstoken'            => $token,
                'wsfunction'         => 'mod_attendance_take_attendance', // Contoh fungsi plugin absensi Moodle
                'moodlewsrestformat' => 'json', // Wajib diset json agar responnya berupa JSON
                'nim'                => $nim,
                'status'             => $status,
                'session_id'         => $sessionId
            ]);

            // Cek apakah transmisi data berhasil (HTTP Status 200)
            if ($response->successful()) {
                $result = $response->json();
                
                // Antisipasi jika Moodle mengembalikan error dalam bentuk JSON body
                if (isset($result['exception'])) {
                    Log::error("Moodle API Exception: " . $result['message']);
                    return false;
                }

                Log::info("Sinkronisasi Moodle Sukses untuk NIM: " . $nim);
                return true;
            } else {
                Log::error("Gagal koneksi ke API Moodle. Status Code: " . $response->status());
                return false;
            }

        } catch (\Exception $e) {
            // Menangkap jika network putus (Skenario nomor 4 di tabel testing kita)
            Log::error("Koneksi Moodle Terputus: " . $e->getMessage());
            return false;
        }
    }

    public function terimaLaporanFlask(Request $request)
    {
        $nim = $request->input('nim');
        $status = $request->input('status_kehadiran', 'Hadir');
        $sessionId = '12'; // Sesi kelas Moodle (sementara kita hardcode dulu untuk simulasi)

        Log::info("Laravel menerima sinyal presensi dari Flask untuk NIM: " . $nim);

        // Langsung panggil fungsi kirim data ke Moodle yang sudah kita buat kemarin
        $moodleStatus = $this->kirimDataKeMoodle($nim, $status, $sessionId);

        return response()->json([
            'status' => 'success',
            'message' => 'Sinyal diterima Laravel. Status sinkronisasi Moodle: ' . ($moodleStatus ? 'Sukses' : 'Gagal')
        ]);
    }
}