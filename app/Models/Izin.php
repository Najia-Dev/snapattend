<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan jika namanya bukan plural dari model
    protected $table = 'izin';

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
