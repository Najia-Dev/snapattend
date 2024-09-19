<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        Setting::create([
            'unit' => 'TK',
            'radius' => 100,
            'latitude' => '-7.220494',
            'longitude' => '112.731922',
            'waktu_masuk_mulai' => '07:00:00',
            'waktu_masuk_akhir' => '08:05:00',
            'waktu_istirahat_mulai' => '12:00:00',
            'waktu_istirahat_akhir' => '13:00:00',
            'waktu_pulang_mulai' => '16:00:00',
            'waktu_pulang_akhir' => '17:00:00',
        ]);
    }
}
