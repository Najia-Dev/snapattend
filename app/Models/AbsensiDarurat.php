<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiDarurat extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan jika namanya bukan plural dari model
    protected $table = 'absensi_darurat';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'alasan',
        'tanggal',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
