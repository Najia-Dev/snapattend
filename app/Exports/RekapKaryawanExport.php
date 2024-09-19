<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;

class RekapKaryawanExport implements FromCollection
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        // Kueri untuk mengambil data absensi berdasarkan bulan dan tahun
        return Absensi::whereMonth('date', $this->bulan)
                      ->whereYear('date', $this->tahun)
                      ->get();
    }
}
