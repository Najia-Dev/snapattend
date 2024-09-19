<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IzinController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_izin' => 'required|date',
            'keterangan' => 'required|string',
            'lampiran_foto' => 'required|image|max:1000', // Validasi file foto
        ]);

        // Simpan foto izin ke dalam folder dan ambil path-nya
        $path = $request->file('lampiran_foto')->store('lampiran_izin', 'public');

        // Simpan data izin ke dalam tabel
        DB::table('izin')->insert([
            'user_id' => auth()->id(),
            'tanggal_izin' => $request->tanggal_izin,
            'keterangan_izin' => $request->keterangan,
            'lampiran_foto' => $path, // Simpan path langsung
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Izin berhasil diajukan.');
    }
}
