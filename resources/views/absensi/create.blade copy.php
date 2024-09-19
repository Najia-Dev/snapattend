@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Tampilkan pesan sukses atau error -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between">
        <h2>FORM ABSENSI</h2>
        <h4 id="clock" class="text-right"></h4>
    </div>

    <!-- Form Absensi -->
    <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="type">Pilihan Waktu</label>
            <select class="form-control" id="type" name="type" required>
                <option value="Masuk">Masuk</option>
                <option value="Istirahat">Istirahat</option>
                <option value="Pulang">Pulang</option>
            </select>
        </div>

        <div class="form-group">
            <label for="camera">Camera Selfie</label>
            <div>
                <video id="video" width="320" height="240" autoplay></video>
                <canvas id="canvas" style="display:none;"></canvas>
            </div>
            <input type="hidden" name="selfie" id="selfie">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>

        <!-- Capture and Submit Buttons -->
        <button type="button" id="capture" class="btn btn-primary">Capture</button>
        <button type="submit" id="submitButton" class="btn btn-success">Submit</button>
    </form>

    <!-- Bagian untuk Rekap Absensi Hari Ini -->
    <h3>REKAP ABSENSI HARI INI</h3>
    <div class="row">
        @foreach(['Masuk', 'Istirahat', 'Pulang'] as $type)
            <div class="col">
                <h4>{{ $type }}</h4>
                <img src="{{ isset($absensi[$type]) ? asset('images/' . $absensi[$type]->proof_photo) : asset('images/placeholder.png') }}" alt="hasil foto" style="width: 150px; height: 150px;">
                <p>Jarak: <span style="color: {{ isset($absensi[$type]) && $absensi[$type]->is_in_radius ? 'green' : 'red' }}">
                    {{ isset($absensi[$type]) ? $absensi[$type]->is_in_radius ? 'Dalam Radius' : 'Luar Radius' : '' }}
                </span></p>
                <p>Waktu: {{ isset($absensi[$type]) ? $absensi[$type]->time : '' }}</p>
                <p>Status: <span style="color: {{ isset($absensi[$type]) && $absensi[$type]->status == 'Berhasil' ? 'green' : 'red' }}">
                    {{ isset($absensi[$type]) ? $absensi[$type]->status : '' }}
                </span></p>
            </div>
        @endforeach
    </div>

    <!-- Bagian untuk Absensi Darurat -->
    <h3>Form Absensi Darurat</h3>
    <form action="{{ route('absensi_darurat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" class="form-control" placeholder="Alasan darurat (macet, ban bocor, dll.)" required></textarea>
        </div>
        <div class="form-group">
            <label for="foto">Bukti Foto</label>
            <input type="file" name="foto" class="form-control" onchange="previewImage(event, 'previewAbsensiDarurat')" required>
            <img id="previewAbsensiDarurat" src="#" alt="Preview Bukti Foto" style="display:none; max-width: 150px; max-height: 150px; margin-top: 10px;" />
        </div>
        <button type="submit" class="btn btn-primary">Kirim Absensi Darurat</button>
    </form>

    <!-- Bagian untuk Form Izin -->
    <h3>Form Izin</h3>
    <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="tanggal_izin">Tanggal Izin</label>
            <input type="date" name="tanggal_izin" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan Izin</label>
            <textarea name="keterangan" class="form-control" placeholder="Alasan pengajuan izin" required></textarea>
        </div>
        <div class="form-group">
            <label for="lampiran_foto">Lampiran Foto</label>
            <input type="file" name="lampiran_foto" class="form-control" onchange="previewImage(event, 'previewIzin')" required>
            <img id="previewIzin" src="#" alt="Preview Lampiran Foto" style="display:none; max-width: 150px; max-height: 150px; margin-top: 10px;" />
        </div>
        <button type="submit" class="btn btn-primary">Ajukan Izin</button>
    </form>
</div>

<!-- JavaScript untuk validasi waktu absensi dan preview foto -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const unit = "{{ auth()->user()->unit }}";
        const submitButton = document.getElementById('submitButton');
        const typeSelect = document.getElementById('type');

        // Waktu absensi per unit dan tipe
        const waktuAbsensi = {
            'TK': { 'masukMulai': '07:00', 'masukAkhir': '08:10' },
            'SD': { 'masukMulai': '07:30', 'masukAkhir': '08:10' },
            'SMP': { 'masukMulai': '06:30', 'masukAkhir': '08:10' },
            'SMA': { 'masukMulai': '06:30', 'masukAkhir': '08:10' },
            'SMK': { 'masukMulai': '06:30', 'masukAkhir': '08:10' },
            'KUPP': { 'masukMulai': '07:30', 'masukAkhir': '08:10' },
            'istirahatMulai': '12:00', 'istirahatAkhir': '13:00',
            'pulangMulai': '16:00', 'pulangAkhir': '23:59'
        };

        // Fungsi untuk validasi waktu absensi
        function validateTime() {
            const selectedType = typeSelect.value;
            const timeNow = new Date();
            let waktuMulai, waktuAkhir;

            // Tentukan waktu validasi berdasarkan tipe absensi
            if (selectedType === 'Masuk') {
                waktuMulai = waktuAbsensi[unit].masukMulai.split(':');
                waktuAkhir = waktuAbsensi[unit].masukAkhir.split(':');
            } else if (selectedType === 'Istirahat') {
                waktuMulai = waktuAbsensi.istirahatMulai.split(':');
                waktuAkhir = waktuAbsensi.istirahatAkhir.split(':');
            } else if (selectedType === 'Pulang') {
                waktuMulai = waktuAbsensi.pulangMulai.split(':');
                waktuAkhir = waktuAbsensi.pulangAkhir.split(':');
            }

            // Set waktu mulai dan akhir
            const mulai = new Date();
            mulai.setHours(waktuMulai[0], waktuMulai[1]);
            const akhir = new Date();
            akhir.setHours(waktuAkhir[0], waktuAkhir[1]);

            // Validasi waktu saat ini
            if (timeNow < mulai || timeNow > akhir) {
                submitButton.disabled = true;
            } else {
                submitButton.disabled = false;
            }
        }

        // Validasi waktu ketika tipe absensi berubah
        typeSelect.addEventListener('change', validateTime);

        // Panggil validasi waktu saat halaman pertama kali dimuat
        validateTime();

        // Akses kamera dan tangkap gambar
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const selfieInput = document.getElementById('selfie');
        const captureButton = document.getElementById('capture');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing the camera: ", err);
            });

        captureButton.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataURL = canvas.toDataURL('image/png');
            selfieInput.value = dataURL;

            // Get location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                });
            }
        });
    });

    // Preview foto untuk form absensi darurat dan izin
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Menampilkan jam real-time
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Jakarta', timeZoneName: 'short' };
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', options);
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
