@extends('layouts.admin')

@section('content')
    <h1>Data Absensi untuk Unit: {{ $unit }}</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Unit</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $data)
                <tr>
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->nama }}</td>
                    <td>{{ $data->unit }}</td>
                    <td>{{ $data->tanggal }}</td>
                    <td>{{ $data->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
