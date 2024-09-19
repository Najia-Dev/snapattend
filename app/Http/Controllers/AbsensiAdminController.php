
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;

class AbsensiAdminController extends Controller
{
    public function showByUnit(Request $request, $unit)
{
    $unitList = ['kupp', 'tk', 'sd', 'smp', 'sma', 'smk'];

    if (!in_array($unit, $unitList)) {
        abort(404);
    }

    // Debug: Cek request tanggal, bulan, dan tahun
    dd($request->get('tanggal'), $request->get('bulan'), $request->get('tahun'));

    $tanggal = $request->get('tanggal');
    $bulan = $request->get('bulan');
    <?php
    
    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use App\Models\Absensi;
    
    class AbsensiAdminController extends Controller
    {
        public function showByUnit(Request $request, $unit)
        {
            $unitList = ['kupp', 'tk', 'sd', 'smp', 'sma', 'smk'];
    
            // Cek apakah unit valid
            if (!in_array($unit, $unitList)) {
                abort(404); // Jika unit tidak valid, tampilkan 404
            }
    
            // Debug: Cek request tanggal, bulan, dan tahun
            dd($request->get('tanggal'), $request->get('bulan'), $request->get('tahun'));
    
            // Ambil data absensi berdasarkan unit dan filter berdasarkan tanggal yang dipilih
            $tanggal = $request->get('tanggal');
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');
    
            $dataAbsensi = Absensi::where('unit', strtoupper($unit))
                ->when($tanggal, function($query) use ($tanggal) {
                    // Prioritaskan filter berdasarkan tanggal penuh (Y-m-d)
                    return $query->whereDate('created_at', $tanggal);
                }, function($query) use ($bulan, $tahun) {
                    // Jika tidak ada tanggal, filter berdasarkan bulan dan tahun
                    return $query->whereMonth('created_at', $bulan)
                                 ->whereYear('created_at', $tahun);
                })
                ->get();
    
            return view('admin.data-absensi', compact('dataAbsensi', 'unit'));
        }
    }
    
    $tahun = $request->get('tahun');

    $dataAbsensi = Absensi::where('unit', strtoupper($unit))
        ->when($tanggal, function($query) use ($tanggal) {
            return $query->whereDate('created_at', $tanggal);
        }, function($query) use ($bulan, $tahun) {
            return $query->whereMonth('created_at', $bulan)
                         ->whereYear('created_at', $tahun);
        })
        ->get();

    return view('admin.data-absensi', compact('dataAbsensi', 'unit'));
}
