<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi'; // Nama tabel di MySQL kamu
    public $timestamps = false;    // Karena kita input manual 'waktu_masuk'
    protected $guarded = [];       // Biar bisa input data apa aja

        public function mahasiswa()
        {
            // Presensi punya hubungan ke Mahasiswa lewat kolom 'nim'
            return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
        }
}

