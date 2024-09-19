<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Mengambil data absensi hari ini untuk user yang sedang login
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->get()
            ->keyBy('type'); // Mengelompokkan berdasarkan tipe absensi (Masuk, Istirahat, Pulang)

        // Mengambil data absensi untuk 1 minggu terakhir
        $absensiMingguan = Absensi::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();

        // Mengambil data absensi untuk 1 bulan terakhir
        $absensiBulanan = Absensi::where('user_id', $user->id)
            ->whereMonth('date', Carbon::now()->month)
            ->get();

        // Mengambil data absensi untuk 1 tahun terakhir
        $absensiTahunan = Absensi::where('user_id', $user->id)
            ->whereYear('date', Carbon::now()->year)
            ->get();

        return view('home', compact('user', 'absensiHariIni', 'absensiMingguan', 'absensiBulanan', 'absensiTahunan'));
    }
}
