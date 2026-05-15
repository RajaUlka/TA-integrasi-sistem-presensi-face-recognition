<?php

namespace App\Http\Controllers;

use App\Models\Presensi; // Import Model Presensi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    
        // 2. Perintah Windows untuk buka CMD baru dan jalankan python di folder tersebut
        // /c artinya jalankan perintah lalu tutup CMD-nya (tapi script python tetap jalan)
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
}