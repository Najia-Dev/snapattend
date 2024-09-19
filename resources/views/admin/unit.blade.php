@extends('layouts.admin')

@section('content')
<div class="admin-unit">
    <h1>Data Karyawan Unit {{ $unit }}</h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Status Kehadiran</th>
                    <th>Status Tidak Hadir</th>
                    <th>Terlambat</th>
                    <th>Izin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($karyawans as $index => $karyawan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $karyawan->nama }}</td>
                    <td>{{ $karyawan->status_kehadiran }}</td>
                    <td>{{ $karyawan->status_tidak_hadir }}</td>
                    <td>{{ $karyawan->terlambat }}</td>
                    <td>{{ $karyawan->izin }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .table {
        margin-top: 20px;
    }
</style>
@endsection
