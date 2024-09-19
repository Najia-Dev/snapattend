<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel; // Tambahkan facade Excel
use App\Exports\AbsensiExport; // Buat export class
use Carbon\Carbon; // Gunakan Carbon untuk tanggal

class AdminController extends Controller
{
    public function dataAbsensi(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $unit = $request->input('unit', '');
        $perPage = $request->input('per_page', 25); // Default tampilkan 25 data

        // Query untuk menggabungkan data absensi per karyawan per tanggal
        $absensiData = Absensi::join('users', 'absensi.user_id', '=', 'users.id')
            ->where('users.role', 'karyawan')
            ->whereMonth('absensi.created_at', $bulan)
            ->whereYear('absensi.created_at', $tahun)
            ->when($unit, function($query, $unit) {
                return $query->where('users.unit', $unit);
            })
            ->paginate($perPage);

        return view('admin.data-absensi', compact('absensiData', 'bulan', 'tahun', 'unit'));
    }

    // Tambahan method index() untuk dashboard
    public function index()
    {
        // Total absensi yang tercatat di database
        $totalAbsensi = Absensi::count();

        // Ambil data karyawan yang hadir hari ini
        $hadirHariIni = Absensi::whereDate('created_at', Carbon::today())->count();

        // Ambil data karyawan yang terlambat (misal jam masuk setelah 08:05)
        $terlambat = Absensi::whereTime('created_at', '>', '08:05:00')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Ambil data karyawan yang tidak hadir hari ini
        $karyawanYangHadir = Absensi::whereDate('created_at', Carbon::today())->pluck('user_id');
        $tidakHadirHariIni = User::whereNotIn('id', $karyawanYangHadir)
            ->where('role', 'karyawan') // Filter berdasarkan role karyawan
            ->get();

        // Kirimkan data ke view
        return view('admin.dashboard', compact('totalAbsensi', 'hadirHariIni', 'terlambat', 'tidakHadirHariIni'));
    }
}
