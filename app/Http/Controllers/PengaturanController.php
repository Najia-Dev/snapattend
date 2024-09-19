<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\RadiusKoordinat;

class PengaturanController extends Controller
{
    public function storeJam(Request $request)
    {
        // Validasi input
        $request->validate([
            'unit' => 'required',
            'tipe' => 'required',
            'jam_mulai' => 'required',
            'jam_akhir' => 'required',
        ]);

        // Simpan data ke database
        Setting::create([
            'unit' => $request->unit,
            'tipe' => $request->tipe,
            'jam_mulai' => $request->jam_mulai,
            'jam_akhir' => $request->jam_akhir,
        ]);

        // Redirect kembali ke halaman dengan pesan sukses
        return redirect()->back()->with('success', 'Pengaturan jam berhasil disimpan.');
    }

    public function storeRadius(Request $request)
    {
        // Validasi input
        $request->validate([
            'radius' => 'required',
            'koordinat' => 'required',
        ]);

        // Simpan data ke database
        RadiusKoordinat::create([
            'radius' => $request->radius,
            'koordinat' => $request->koordinat,
        ]);

        // Redirect kembali ke halaman dengan pesan sukses
        return redirect()->back()->with('success', 'Pengaturan radius dan koordinat berhasil disimpan.');
    }
}