<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RekapAbsensiController extends Controller
{
    public function getMingguan()
    {
        return $this->getDataForPeriod('week');
    }

    public function getBulanan()
    {
        return $this->getDataForPeriod('month');
    }

    public function getTahunan()
    {
        return $this->getDataForPeriod('year');
    }

    private function getDataForPeriod($period)
    {
        $query = Absensi::where('user_id', Auth::id());

        if ($period == 'week') {
            $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($period == 'year') {
            $query->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
        }

        $absensi = $query->get();
        
        $data = [
            'totalHadir' => $absensi->where('status', 'Hadir')->count(),
            'totalTerlambat' => $absensi->where('status', 'Terlambat')->count(),
            'totalTidakHadir' => $absensi->where('status', 'Tidak Hadir')->count(),
            'totalIzin' => $absensi->where('status', 'Izin')->count(),
            'detail' => $absensi
        ];

        return response()->json($data);
    }

    public function rekapBulanan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Query absensi berdasarkan bulan dan tahun
        $data = Absensi::where('user_id', Auth::id())
                       ->whereMonth('created_at', $bulan)
                       ->whereYear('created_at', $tahun)
                       ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data untuk bulan dan tahun tersebut.'], 404);
        }

        $data = [
            'totalHadir' => $data->where('status', 'Hadir')->count(),
            'totalTerlambat' => $data->where('status', 'Terlambat')->count(),
            'totalTidakHadir' => $data->where('status', 'Tidak Hadir')->count(),
            'totalIzin' => $data->where('status', 'Izin')->count(),
            'detail' => $data
        ];

        return response()->json($data);
    }

    public function rekapTahunan(Request $request)
    {
        $tahun = $request->input('tahun');

        // Query absensi berdasarkan tahun
        $data = Absensi::where('user_id', Auth::id())
                       ->whereYear('created_at', $tahun)
                       ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data untuk tahun tersebut.'], 404);
        }

        $data = [
            'totalHadir' => $data->where('status', 'Hadir')->count(),
            'totalTerlambat' => $data->where('status', 'Terlambat')->count(),
            'totalTidakHadir' => $data->where('status', 'Tidak Hadir')->count(),
            'totalIzin' => $data->where('status', 'Izin')->count(),
            'detail' => $data
        ];

        return response()->json($data);
    }
}
