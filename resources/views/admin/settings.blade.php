
@extends('layouts.admin')

@section('content')
    <h1>Pengaturan Sistem</h1>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="mb-3">
            <label for="radius" class="form-label">Radius Absensi (meter)</label>
            <input type="number" name="radius" id="radius" class="form-control" value="{{ old('radius', $currentRadius) }}">
        </div>
        <div class="mb-3">
            <label for="jam_masuk" class="form-label">Jam Masuk</label>
            <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" value="{{ old('jam_masuk', $currentJamMasuk) }}">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
    </form>
@endsection
