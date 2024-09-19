<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    protected $bulan, $tahun, $unit;

    public function __construct($bulan, $tahun, $unit)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->unit = $unit;
    }

    public function query()
    {
        $query = Absensi::join('users', 'absensi.user_id', '=', 'users.id')
            ->where('users.role', 'karyawan')
            ->whereMonth('absensi.created_at', $this->bulan)
            ->whereYear('absensi.created_at', $this->tahun)
            ->when($this->unit, function($query, $unit) {
                return $query->where('users.unit', $this->unit);
            })
            ->select(
                'users.name as nama_karyawan', 
                'users.unit', 
                \DB::raw("MAX(CASE WHEN absensi.type = 'masuk' THEN TIME(absensi.created_at) END) as masuk"),
                \DB::raw("MAX(CASE WHEN absensi.type = 'istirahat' THEN TIME(absensi.created_at) END) as istirahat"),
                \DB::raw("MAX(CASE WHEN absensi.type = 'pulang' THEN TIME(absensi.created_at) END) as pulang"),
                \DB::raw("DATE(absensi.created_at) as tanggal_absensi")
            )
            ->groupBy('users.name', 'users.unit', \DB::raw('DATE(absensi.created_at)'));

        return $query;
    }

    public function headings(): array
    {
        return ['Nama Karyawan', 'Unit', 'Masuk', 'Istirahat', 'Pulang', 'Tanggal Absensi'];
    }
}
