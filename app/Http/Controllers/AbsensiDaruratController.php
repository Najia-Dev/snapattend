<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiDaruratController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string',
            'foto' => 'required|image|max:1000', // Validasi file foto
        ]);

        // Simpan foto dan ambil path-nya
        $path = $request->file('foto')->store('bukti_absensi_darurat', 'public');

        // Simpan data ke database
        DB::table('absensi_darurat')->insert([
            'user_id' => auth()->id(),
            'keterangan' => $request->keterangan,
            'foto' => $path, // Simpan path langsung
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Absensi Darurat berhasil dikirim.');
    }
}
