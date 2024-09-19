@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h4>Pengaturan Sistem</h4>
    <!-- Menampilkan form untuk setiap unit -->
    @foreach(['KUPP', 'TK', 'SD', 'SMP', 'SMA', 'SMK'] as $unit)
    @php
        // Ambil pengaturan yang ada di database untuk unit ini
        $currentSetting = $settings->where('unit', $unit)->first();
    @endphp
    <form action="{{ route('admin.updateSettings') }}" method="POST" class="mb-4">
        @csrf
        <div class="card">
            <div class="card-body">
                <!-- Bagian Kiri: Unit, Radius, Latitude, Longitude -->
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Unit:</strong> {{ $unit }}</p>
                        <input type="hidden" name="unit" value="{{ $unit }}">
                        <p><strong>Radius:</strong> 
                            <input type="number" name="radius" class="form-control" placeholder="Meter" required value="{{ $currentSetting->radius ?? '' }}">
                        </p>
                        <p><strong>Latitude:</strong> 
                            <input type="text" name="latitude" class="form-control" placeholder="Latitude" required value="{{ $currentSetting->latitude ?? '' }}">
                        </p>
                        <p><strong>Longitude:</strong> 
                            <input type="text" name="longitude" class="form-control" placeholder="Longitude" required value="{{ $currentSetting->longitude ?? '' }}">
                        </p>
                    </div>
                    <!-- Bagian Kanan: Pengaturan Waktu Absensi -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Masuk</h6>
                                <p><strong>Jam Mulai:</strong> 
                                    <input type="time" name="waktu_masuk_mulai" class="form-control" required value="{{ $currentSetting->waktu_masuk_mulai ?? '' }}">
                                </p>
                                <p><strong>Jam Akhir:</strong> 
                                    <input type="time" name="waktu_masuk_akhir" class="form-control" required value="{{ $currentSetting->waktu_masuk_akhir ?? '' }}">
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>Istirahat</h6>
                                <p><strong>Jam Mulai:</strong> 
                                    <input type="time" name="waktu_istirahat_mulai" class="form-control" required value="{{ $currentSetting->waktu_istirahat_mulai ?? '' }}">
                                </p>
                                <p><strong>Jam Akhir:</strong> 
                                    <input type="time" name="waktu_istirahat_akhir" class="form-control" required value="{{ $currentSetting->waktu_istirahat_akhir ?? '' }}">
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>Pulang</h6>
                                <p><strong>Jam Mulai:</strong> 
                                    <input type="time" name="waktu_pulang_mulai" class="form-control" required value="{{ $currentSetting->waktu_pulang_mulai ?? '' }}">
                                </p>
                                <p><strong>Jam Akhir:</strong> 
                                    <input type="time" name="waktu_pulang_akhir" class="form-control" required value="{{ $currentSetting->waktu_pulang_akhir ?? '' }}">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tombol Simpan -->
                <button type="submit" class="btn btn-primary mt-3">Simpan Pengaturan</button>

                <!-- Tanda berhasil disimpan -->
                @if(session('success') && session('updated_unit') == $unit)
                    <div class="alert alert-success mt-3">
                        Pengaturan berhasil disimpan untuk unit {{ $unit }}.
                    </div>
                @endif
            </div>
        </div>
    </form>
    @endforeach

    <!-- Feedback Section -->
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
