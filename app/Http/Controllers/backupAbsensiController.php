<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapKaryawanExport;

class AbsensiController extends Controller
{
    // Method untuk menampilkan form absensi dan rekap absensi hari ini
    public function create()
    {
        // Mengambil data absensi hari ini untuk user yang sedang login
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->get()
            ->keyBy('type'); // Mengelompokkan berdasarkan tipe absensi (Masuk, Istirahat, Pulang)

        return view('absensi.create', compact('absensi'));
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

        // Pengaturan waktu absensi berdasarkan unit
        $waktuAbsensi = [
            'TK' => ['masuk_mulai' => '07:00', 'masuk_akhir' => '08:10'],
            'SD' => ['masuk_mulai' => '07:30', 'masuk_akhir' => '08:10'],
            'SMP' => ['masuk_mulai' => '06:30', 'masuk_akhir' => '08:10'],
            'SMA' => ['masuk_mulai' => '06:30', 'masuk_akhir' => '08:10'],
            'SMK' => ['masuk_mulai' => '06:30', 'masuk_akhir' => '08:10'],
            'KUPP' => ['masuk_mulai' => '07:30', 'masuk_akhir' => '08:10']
        ];

        // Inisialisasi status dengan nilai default
        $status = 'Berhasil';

        // Validasi waktu absen berdasarkan tipe
        if ($request->type == 'Masuk') {
            $waktuMulai = Carbon::createFromTimeString($waktuAbsensi[$unit]['masuk_mulai']);
            $waktuAkhir = Carbon::createFromTimeString($waktuAbsensi[$unit]['masuk_akhir']);

            if ($current_time->lt($waktuMulai)) {
                return redirect()->back()->withErrors('Absensi belum dibuka untuk unit Anda.');
            } elseif ($current_time->gt($waktuAkhir)) {
                $status = 'Terlambat';
            }
        } elseif ($request->type == 'Istirahat') {
            $waktuMulai = Carbon::createFromTime(12, 0);
            $waktuAkhir = Carbon::createFromTime(13, 0);

            if ($current_time->lt($waktuMulai) || $current_time->gt($waktuAkhir)) {
                return redirect()->back()->withErrors('Waktu absensi istirahat tidak valid.');
            }
        } elseif ($request->type == 'Pulang') {
            $waktuMulai = Carbon::createFromTime(16, 0);
            $waktuAkhir = Carbon::createFromTime(23, 59);

            if ($current_time->lt($waktuMulai) || $current_time->gt($waktuAkhir)) {
                return redirect()->back()->withErrors('Waktu absensi pulang tidak valid.');
            }
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

        $absensi->is_in_radius = $this->calculateRadius($request->latitude, $request->longitude);

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
    private function calculateRadius($user_lat, $user_lng)
    {
        $radius = 200;
        $office_lat = -7.243062;
        $office_lng = 112.723061;

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
