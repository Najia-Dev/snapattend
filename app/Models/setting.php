<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit',
        'radius',
        'latitude',
        'longitude',
        'waktu_masuk_mulai',
        'waktu_masuk_akhir',
        'waktu_istirahat_mulai',
        'waktu_istirahat_akhir',
        'waktu_pulang_mulai',
        'waktu_pulang_akhir',
    ];
}
