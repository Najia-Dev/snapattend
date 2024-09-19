@extends('layouts.admin')

@section('content')
<div class="data-absensi">
    <h1>Data Absensi</h1>

    <!-- Form Filter Dropdowns -->
    <form method="GET" action="{{ route('admin.dataAbsensi') }}">
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="tanggal">Pilih Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <label for="bulan">Pilih Bulan:</label>
                <select name="bulan" id="bulan" class="form-control">
                    @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                        <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun">Pilih Tahun:</label>
                <select name="tahun" id="tahun" class="form-control">
                    @for($i = 2020; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="unit">Pilih Unit:</label>
                <select name="unit" id="unit" class="form-control">
                    <option value="kupp" {{ request('unit') == 'kupp' ? 'selected' : '' }}>KUPP</option>
                    <option value="tk" {{ request('unit') == 'tk' ? 'selected' : '' }}>TK</option>
                    <option value="sd" {{ request('unit') == 'sd' ? 'selected' : '' }}>SD</option>
                    <option value="smp" {{ request('unit') == 'smp' ? 'selected' : '' }}>SMP</option>
                    <option value="sma" {{ request('unit') == 'sma' ? 'selected' : '' }}>SMA</option>
                    <option value="smk" {{ request('unit') == 'smk' ? 'selected' : '' }}>SMK</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary mt-2">Tampilkan Data</button>
            </div>
        </div>
    </form>

    <!-- Tombol Export -->
    <form method="GET" action="{{ route('admin.exportAbsensi') }}" style="margin-top: 10px;">
        <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
        <input type="hidden" name="unit" value="{{ request('unit') }}">
        <button type="submit" class="btn btn-success">Export</button>
    </form>

    <!-- Tombol Hide/Show Tabel -->
    <button id="toggleTable" class="btn btn-secondary mb-3">Hide Tabel</button>

    <!-- Tabel Data Absensi -->
    <div class="table-responsive" id="tableAbsensi">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Unit</th>
                    <th>Masuk</th>
                    <th>Istirahat</th>
                    <th>Pulang</th>
                    <th>Tanggal Absensi</th>
                </tr>
            </thead>
            <tbody>
                @if(count($absensiData) > 0)
                    @foreach($absensiData as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data->nama_karyawan }}</td>
                            <td>{{ $data->unit }}</td>
                            <td>{{ $data->masuk }}</td>
                            <td>{{ $data->istirahat }}</td>
                            <td>{{ $data->pulang }}</td>
                            <td>{{ $data->tanggal_absensi }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">Data absensi tidak tersedia</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination cleaned of any large icons -->
    <div class="d-flex justify-content-center">
        {{ $absensiData->appends(request()->query())->links() }}
    </div>

    <!-- Tabel Absensi Darurat -->
    <div class="table-responsive mt-5">
        <h2>Absensi Darurat</h2>

        <!-- Form untuk Absensi Darurat -->
        <form method="POST" action="{{ route('absensi_darurat.store') }}">
            @csrf
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="bulan">Pilih Bulan:</label>
                    <select name="bulan" id="bulan" class="form-control">
                        @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun">Pilih Tahun:</label>
                    <select name="tahun" id="tahun" class="form-control">
                        @for($i = 2020; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success mb-4">Tampilkan</button>
        </form>

        <!-- Tombol Hide/Show Tabel Darurat -->
        <button id="toggleTableDarurat" class="btn btn-secondary btn-sm mb-3">Hide Tabel</button>

        <!-- Tabel Data Absensi Darurat -->
        <div id="tableDarurat" class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Unit</th>
                        <th>Alasan Darurat</th>
                        <th>Foto Bukti</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensiDarurat as $index => $darurat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $darurat->nama_karyawan }}</td>
                            <td>{{ $darurat->unit }}</td>
                            <td>{{ $darurat->alasan }}</td>
                            <td>
                                @if($darurat->foto)
                                <img src="{{ asset('storage/' . $darurat->foto) }}" alt="Bukti Foto" width="100" height="100">
                                @else
                                    Tidak ada bukti
                                @endif
                            </td>
                            <td>{{ $darurat->tanggal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Izin -->
    <div class="table-responsive mt-5">
        <h2>Izin</h2>

        <!-- Form untuk Izin -->
        <form method="POST" action="{{ route('izin.store') }}">
            @csrf
            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="bulan">Pilih Bulan:</label>
                    <select name="bulan" id="bulan" class="form-control">
                        @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun">Pilih Tahun:</label>
                    <select name="tahun" id="tahun" class="form-control">
                        @for($i = 2020; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success mb-4">Tampilkan</button>
        </form>

        <!-- Tombol Hide/Show Tabel Izin -->
        <button id="toggleTableIzin" class="btn btn-secondary btn-sm mb-3">Hide Tabel</button>

        <!-- Tabel Data Izin -->
        <div id="tableIzin" class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Unit</th>
                        <th>Alasan Izin</th>
                        <th>Lampiran Foto</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($izin as $index => $izinItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $izinItem->nama_karyawan }}</td>
                            <td>{{ $izinItem->unit }}</td>
                            <td>{{ $izinItem->alasan }}</td>
                            <td>
                                @if($izinItem->lampiran_foto)
                                <img src="{{ asset('storage/' . $izinItem->lampiran_foto) }}" alt="Lampiran Foto" width="100" height="100">
                                @else
                                    Tidak ada lampiran
                                @endif
                            </td>
                            <td>{{ $izinItem->tanggal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSS untuk memperbaiki z-index dan margin -->
<style>
    .ui-datepicker {
        z-index: 10000 !important;
    }

    .data-absensi {
        margin-top: 20px;
    }

    .datepicker {
        margin-bottom: 20px;
    }

    #toggleTable {
        margin-top: 10px;
    }

    .table-responsive {
        margin-top: 30px;
    }

    /* Hide large icons from pagination */
    .pagination .page-item .page-link svg {
        display: none !important;
    }
</style>

<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Script for toggling the table visibility -->
<script>
    $(document).ready(function() {
        $('#toggleTable').on('click', function() {
            $('#tableAbsensi').toggle();
            if ($('#tableAbsensi').is(':visible')) {
                $(this).text('Hide Tabel');
            } else {
                $(this).text('Show Tabel');
            }
        });

        $('#toggleTableDarurat').on('click', function() {
            $('#tableDarurat').toggle();
            if ($('#tableDarurat').is(':visible')) {
                $(this).text('Hide Tabel');
            } else {
                $(this).text('Show Tabel');
            }
        });

        $('#toggleTableIzin').on('click', function() {
            $('#tableIzin').toggle();
            if ($('#tableIzin').is(':visible')) {
                $(this).text('Hide Tabel');
            } else {
                $(this).text('Show Tabel');
            }
        });
    });
</script>
@endsection
