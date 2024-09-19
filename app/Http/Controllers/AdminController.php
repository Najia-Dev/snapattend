<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\AbsensiDarurat;
use App\Models\Izin;
use App\Models\User;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use Carbon\Carbon;
use App\Exports\RekapKaryawanExport;

class AdminController extends Controller
{
    // Method untuk menampilkan dashboard admin
    public function index()
    {
        $totalAbsensi = Absensi::count();
        $hadirHariIni = Absensi::whereDate('created_at', Carbon::today())->count();

        // Ambil waktu batas keterlambatan dari pengaturan di database
        $unit = 'Unit yang dipilih'; // Ganti dengan logika untuk unit yang sedang dipantau
        $waktuMasukAkhir = Setting::where('unit', $unit)->value('waktu_masuk_akhir');

        // Jika tidak ada pengaturan waktu masuk, gunakan waktu default (fallback)
        $waktuMasukAkhir = $waktuMasukAkhir ?? '08:05:00';

        // Menghitung jumlah karyawan yang terlambat berdasarkan pengaturan waktu masuk
        $terlambat = Absensi::whereTime('created_at', '>', $waktuMasukAkhir)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $karyawanYangHadir = Absensi::whereDate('created_at', Carbon::today())->pluck('user_id');
        $tidakHadirHariIni = User::whereNotIn('id', $karyawanYangHadir)
            ->where('role', 'karyawan')
            ->count();

        return view('admin.dashboard', compact('totalAbsensi', 'hadirHariIni', 'terlambat', 'tidakHadirHariIni'));
    }

    // Method untuk menampilkan halaman pengaturan sistem
    public function showSettings()
    {
        // Ambil semua data pengaturan dari tabel settings
        $settings = Setting::all();
        return view('admin.pengaturan-sistem', compact('settings'));
    }

    // Method untuk memperbarui pengaturan sistem
    public function updateSettings(Request $request)
    {
        // Validasi input
        $request->validate([
            'unit' => 'required|string|max:50',
            'radius' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'waktu_masuk_mulai' => 'required',
            'waktu_masuk_akhir' => 'required',
            'waktu_istirahat_mulai' => 'required',
            'waktu_istirahat_akhir' => 'required',
            'waktu_pulang_mulai' => 'required',
            'waktu_pulang_akhir' => 'required',
        ]);

        // Perbarui atau buat pengaturan untuk unit yang dipilih
        $setting = Setting::updateOrCreate(
            ['unit' => $request->unit],
            [
                'radius' => $request->radius,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'waktu_masuk_mulai' => $request->waktu_masuk_mulai,
                'waktu_masuk_akhir' => $request->waktu_masuk_akhir,
                'waktu_istirahat_mulai' => $request->waktu_istirahat_mulai,
                'waktu_istirahat_akhir' => $request->waktu_istirahat_akhir,
                'waktu_pulang_mulai' => $request->waktu_pulang_mulai,
                'waktu_pulang_akhir' => $request->waktu_pulang_akhir,
            ]
        );

        // Simpan pengaturan ke sesi untuk ditampilkan
        return redirect()->route('admin.pengaturanSistem')->with('success', 'Pengaturan berhasil disimpan!')->with('updated_unit', $request->unit);
    }

    // Method untuk menampilkan data absensi dengan filter bulan, tahun, dan unit
    public function dataAbsensi(Request $request)
    {
        $tanggal = $request->input('tanggal', null);
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $unit = $request->input('unit', '');
        $perPage = $request->input('per_page', 25);

        $absensiData = Absensi::join('users', 'absensi.user_id', '=', 'users.id')
            ->where('users.role', 'karyawan')
            ->when($tanggal, function($query, $tanggal) {
                return $query->whereDate('absensi.created_at', $tanggal);
            }, function($query) use ($bulan, $tahun) {
                return $query->whereMonth('absensi.created_at', $bulan)
                             ->whereYear('absensi.created_at', $tahun);
            })
            ->when($unit, function($query, $unit) {
                return $query->where('users.unit', $unit);
            })
            ->select(
                'users.name as nama_karyawan',
                'users.unit',
                \DB::raw("MAX(CASE WHEN absensi.type = 'masuk' THEN TIME(absensi.created_at) END) as masuk"),
                \DB::raw("MAX(CASE WHEN absensi.type = 'istirahat' THEN TIME(absensi.created_at) END) as istirahat"),
                \DB::raw("MAX(CASE WHEN absensi.type = 'pulang' THEN TIME(absensi.created_at) END) as pulang"),
                \DB::raw("DATE(absensi.created_at) as tanggal_absensi")
            )
            ->groupBy('users.name', 'users.unit', \DB::raw('DATE(absensi.created_at)'))
            ->paginate($perPage);

        $absensiDarurat = AbsensiDarurat::join('users', 'absensi_darurat.user_id', '=', 'users.id')
            ->select('users.name as nama_karyawan', 'users.unit', 'absensi_darurat.keterangan as alasan', 'absensi_darurat.foto', 'absensi_darurat.created_at as tanggal')
            ->get();

        $izin = Izin::join('users', 'izin.user_id', '=', 'users.id')
            ->select('users.name as nama_karyawan', 'users.unit', 'izin.keterangan_izin as alasan', 'izin.tanggal_izin as tanggal', 'izin.lampiran_foto')
            ->get();

        return view('admin.data-absensi', compact('absensiData', 'bulan', 'tahun', 'unit', 'perPage', 'absensiDarurat', 'izin'));
    }

    // Method untuk menampilkan halaman manajemen users
    public function manajemenUsers()
    {
        $users = User::all();
        return view('admin.manajemen-users', compact('users'));
    }

    // Method untuk menambah user baru
    public function storeUser(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'unit' => 'required',
            'jabatan' => 'required',
        ]);

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'],
            'unit' => $validatedData['unit'],
            'jabatan' => $validatedData['jabatan'],
        ]);

        return redirect()->route('admin.manajemenUsers')->with('success', 'User berhasil ditambahkan');
    }

    // Method untuk mengedit user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    // Method untuk memperbarui user
    public function updateUser(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'unit' => 'required',
            'jabatan' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->update($validatedData);

        return redirect()->route('admin.manajemenUsers')->with('success', 'User berhasil diperbarui');
    }

    // Method untuk menghapus user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.manajemenUsers')->with('success', 'User berhasil dihapus');
    }

    // Method untuk export data absensi ke Excel
    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $unit = $request->input('unit', '');

        return Excel::download(new AbsensiExport($bulan, $tahun, $unit), 'data-absensi.xlsx');
    }
}
