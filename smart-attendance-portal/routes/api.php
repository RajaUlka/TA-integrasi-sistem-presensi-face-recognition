<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/moodle-sync', function (Request $request) {
    $nim = $request->input('nim');
    $statusKehadiran = $request->input('status_kehadiran');

    $url = env('MOODLE_URL');
    $token = env('MOODLE_TOKEN');

    $response = Http::asForm()->post($url, [
        'wstoken' => $token,
        'wsfunction' => 'core_user_create_users',
        'moodlewsrestformat' => 'json',
        'users' => [
            [
                'username' => strtolower($nim),
                'password' => 'MhsAbsen123!',
                'firstname' => 'Mahasiswa',
                'lastname' => $nim,
                'email' => strtolower($nim) . '@student.ac.id',
            ]
        ]
    ]);

    return response()->json([
        'status' => 'Sistem Pusat Sukses!',
        'moodle_response' => $response->json()
    ]);
});