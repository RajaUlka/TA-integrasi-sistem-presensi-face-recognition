<?php

use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [PresensiController::class, 'index']);
Route::get('/jalankan-python', [PresensiController::class, 'jalankanPython'])->name('jalankan.python');
Route::get('/login', function() { return "Halaman Login Belum Dibuat"; })->name('login');


Route::get('/scan', [PresensiController::class, 'scan'])->name('scan');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/test-moodle', function () {
    $url = env('MOODLE_URL');
    $token = env('MOODLE_TOKEN');

    // Data user dummy yang seolah-olah dikirim dari sistem AI
    $response = \Illuminate\Support\Facades\Http::asForm()->post($url, [
        'wstoken' => $token,
        'wsfunction' => 'core_user_create_users',
        'moodlewsrestformat' => 'json',
        'users' => [
            [
                'username' => 'mahasiswa_ai',
                'password' => 'MhsAI123!', 
                'firstname' => 'Budi',
                'lastname' => 'Presensi AI',
                'email' => 'budi.ai@example.com',
            ]
        ]
    ]);

    return $response->json();
});


// --- ROUTE ADMIN (DIKUNCI PAKAI SESSION) ---
// --- ROUTE ADMIN (DIKUNCI PAKAI SESSION) ---
Route::group([
    'prefix' => 'admin', 
    'middleware' => [function (\Illuminate\Http\Request $request, $next) { // <-- 1. Tambahkan type-hint Request di sini
        // 2. Ganti session() menjadi $request->session()
        if (!$request->session()->has('admin_logged_in')) { 
            return redirect()->route('login')->with('error', 'Anda harus login dulu!');
        }
        return $next($request);
    }]
], function () {
    
    // Taruh SEMUA route admin.dashboard, matakuliah, dll di sini
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/register-face', [AdminController::class, 'registerFace'])->name('admin.register_face');
    Route::post('/register-face', [AdminController::class, 'storeFace'])->name('admin.register_face.store');
    Route::delete('/mahasiswa/{nim}', [AdminController::class, 'destroyMahasiswa'])->name('admin.mahasiswa.destroy');
    Route::post('/admin/presensi/purge', [AdminController::class, 'purgePresensi'])->name('admin.presensi.purge');
    
    Route::get('/matakuliah', [AdminController::class, 'matakuliah'])->name('admin.matakuliah');
    Route::post('/matakuliah', [AdminController::class, 'storeMatakuliah'])->name('admin.matakuliah.store');
    Route::delete('/matakuliah/{kode}', [AdminController::class, 'destroyMatakuliah'])->name('admin.matakuliah.destroy');
    
    Route::delete('/presensi/{id}', [AdminController::class, 'destroyPresensi'])->name('admin.presensi.destroy');
    
    // API untuk ambil data matkul otomatis
    Route::get('/matakuliah/api/{kode}', [AdminController::class, 'getMatakuliahApi']);
});  