<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan
    protected $table = 'absensi';

    // Menentukan kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'type',
        'is_in_radius',
        'liveness_verified',
        'status',
        'reason',
        'proof_photo',
    ];
}
