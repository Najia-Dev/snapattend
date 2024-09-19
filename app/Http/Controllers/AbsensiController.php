<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapKaryawanExport;

class AbsensiController extends Controller
{
    // Method untuk menampilkan form absensi dan rekap absensi hari ini
    public function create()
    {
        // Mengambil pengaturan waktu absensi dari database untuk unit pengguna yang sedang login
        $unit = Auth::user()->unit;
        $pengaturan = Setting::where('unit', $unit)->get();

        // Mengambil data absensi hari ini untuk user yang sedang login
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->get()
            ->keyBy('type'); // Mengelompokkan berdasarkan tipe absensi (Masuk, Istirahat, Pulang)

        return view('absensi.create', compact('absensi', 'pengaturan'));
    }

    // Method untuk menyimpan data absensi
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'selfie' => 'required|string',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $current_time = Carbon::now();
        $unit = Auth::user()->unit;

        // Ambil pengaturan dari database berdasarkan unit
        $pengaturan = Setting::where('unit', $unit)->first();

        // Jika pengaturan tidak ditemukan, kirim error
        if (!$pengaturan) {
            return redirect()->back()->with('error', 'Pengaturan absensi tidak ditemukan.');
        }

        // Inisialisasi status dengan nilai default
        $status = 'Terlambat';

        // Cek apakah waktu absensi berada di dalam rentang waktu yang diizinkan
        if ($current_time->format('H:i:s') >= $pengaturan->waktu_masuk_mulai && $current_time->format('H:i:s') <= $pengaturan->waktu_masuk_akhir) {
            $status = 'Berhasil';
        }

        // Cek apakah sudah absen untuk tipe yang sama hari ini
        $existingAbsensi = Absensi::where('user_id', Auth::id())
            ->where('type', $request->type)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($existingAbsensi) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi untuk ' . $request->type . ' hari ini.');
        }

        // Simpan absensi ke database
        $absensi = new Absensi();
        $absensi->user_id = Auth::id();
        $absensi->date = $current_time->toDateString();
        $absensi->time = $current_time->toTimeString();
        $absensi->type = $request->type;
        $absensi->status = $status;
        $absensi->proof_photo = $this->saveSelfie($request->selfie);

        if ($request->has('reason')) {
            $absensi->reason = $request->reason;
        }

        if ($request->hasFile('proof')) {
            $fileName = time() . '_' . $request->file('proof')->getClientOriginalName();
            $request->file('proof')->move(public_path('proofs'), $fileName);
            $absensi->proofs = $fileName;
        }

        // Menghitung radius dan menyimpan status ke database
        $absensi->is_in_radius = $this->calculateRadius($request->latitude, $request->longitude, $pengaturan->latitude, $pengaturan->longitude, $pengaturan->radius);

        $absensi->save();

        return redirect()->route('absensi.create')->with('success', 'Absensi berhasil disimpan!');
    }

    // Method untuk menyimpan selfie
    private function saveSelfie($selfieData)
    {
        $image = str_replace('data:image/png;base64,', '', $selfieData);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '.png';

        $imagePath = public_path('images');
        if (!\File::exists($imagePath)) {
            \File::makeDirectory($imagePath, 0755, true);
        }

        \File::put($imagePath . '/' . $imageName, base64_decode($image));
        return $imageName;
    }

    // Method untuk menghitung radius lokasi
    private function calculateRadius($user_lat, $user_lng, $office_lat, $office_lng, $radius)
    {
        $distance = $this->haversineGreatCircleDistance($user_lat, $user_lng, $office_lat, $office_lng);

        return $distance <= $radius;
    }

    // Method untuk menghitung jarak dengan rumus haversine
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $longitudeFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    // Method untuk mengekspor data absensi ke Excel
    public function export(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if (!$bulan || !$tahun) {
            return redirect()->back()->withErrors('Bulan dan Tahun harus dipilih.');
        }

        return Excel::download(new RekapKaryawanExport($bulan, $tahun), 'rekap_absensi_' . $bulan . '_' . $tahun . '.xlsx');
    }
}
