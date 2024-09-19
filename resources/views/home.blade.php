@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Profil Pengguna -->
    <div class="row mb-4">
        <div class="col-md-4 text-center">
            <div class="profile-picture-wrapper" style="position: relative; width: 150px; height: 150px; overflow: hidden; margin: auto;">
                <img id="profileImage" src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/placeholder.png') }}" 
                     alt="Foto Profil" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
            </div>
            <form action="{{ route('profile.update_photo') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <input type="file" name="photo" id="photo" class="form-control-file" onchange="previewImage(event)">
                <button type="submit" class="btn btn-primary mt-2">Update Foto</button>
            </form>
        </div>
        <div class="col-md-8">
            <h3 class="text-success">Selamat datang di Snapattend</h3>
            <p>Nama: {{ $user->name }}</p>
            <p>Unit: {{ $user->unit }}</p>
            <p>Jabatan: {{ $user->jabatan }}</p>
            <!-- Tombol Link ke Halaman Absensi -->
            <a href="{{ route('absensi.create') }}" class="btn btn-success btn-lg mt-4">Mulai Absensi</a>
        </div>
    </div>

    <!-- Rekap Absensi Hari Ini -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 id="currentTime" class="text-center text-primary">ABSENSI HARI INI</h4>
            <div class="row">
                @foreach(['Masuk', 'Istirahat', 'Pulang'] as $type)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $type }}</h5>
                                <p>Jarak: <span class="{{ isset($absensiHariIni[$type]) && $absensiHariIni[$type]->is_in_radius ? 'text-success' : 'text-danger' }}">
                                    {{ isset($absensiHariIni[$type]) ? ($absensiHariIni[$type]->is_in_radius ? 'Dalam Radius' : 'Luar Radius') : '-' }}
                                </span></p>
                                <p>Waktu: {{ isset($absensiHariIni[$type]) ? $absensiHariIni[$type]->time : '-' }}</p>
                                <p>Status: <span class="{{ isset($absensiHariIni[$type]) && $absensiHariIni[$type]->status == 'Berhasil' ? 'text-success' : 'text-danger' }}">
                                    {{ isset($absensiHariIni[$type]) ? $absensiHariIni[$type]->status : '-' }}
                                </span></p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RINGKASAN REKAP Secara Horizontal -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h3 class="text-info">RINGKASAN REKAP</h3>
            <div class="d-flex justify-content-around flex-wrap">
                <div class="ringkasan-box">
                    <h5>Total Hadir</h5>
                    <span id="totalHadir">0</span>
                </div>
                <div class="ringkasan-box">
                    <h5>Total Terlambat</h5>
                    <span id="totalTerlambat">0</span>
                </div>
                <div class="ringkasan-box">
                    <h5>Total Izin</h5>
                    <span id="totalIzin">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pilihan Rekap Bulan dan Tahun -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Pilihan Bulan</h5>
            <select id="rekapBulan" class="form-control">
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
        </div>

        <div class="col-md-6">
            <h5>Pilihan Tahun</h5>
            <select id="rekapTahun" class="form-control">
                @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div id="rekapSummary" class="row mb-4" style="display: none;">
        <div class="col-md-12">
            <button id="toggleTable" class="btn btn-info mt-2">Show / Hide</button>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div id="rekapTable" class="row mb-4" style="display: none;">
        <div class="col-md-12">
            <div id="rekapContent"></div>
        </div>
    </div>

    <!-- Statistik Kehadiran -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h4>Statistik Kehadiran</h4>
            <canvas id="attendanceChart" style="max-width: 400px; max-height: 400px;"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
// Sinkronisasi data chart dengan ringkasan
function updateChart(data) {
    attendanceChart.data.datasets[0].data = [data.totalHadir, data.totalTerlambat, data.totalIzin];
    attendanceChart.update();
}

// Script untuk preview foto sebelum submit
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('profileImage');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

// Script untuk menampilkan ringkasan dan tabel rekap berdasarkan pilihan bulan dan tahun
document.getElementById('rekapBulan').addEventListener('change', loadRekapData);
document.getElementById('rekapTahun').addEventListener('change', loadRekapData);

function loadRekapData() {
    const bulan = document.getElementById('rekapBulan').value;
    const tahun = document.getElementById('rekapTahun').value;
    let url = `{{ url('/rekap/bulanan') }}?bulan=${bulan}&tahun=${tahun}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const totalHadir = data.detail.filter(absensi => absensi.type === 'Masuk').length;
            $('#rekapSummary').show();
            $('#totalHadir').text(totalHadir);
            $('#totalTerlambat').text(data.totalTerlambat);
            $('#totalIzin').text(data.totalIzin);

            updateChart({
                totalHadir: totalHadir,
                totalTerlambat: data.totalTerlambat,
                totalIzin: data.totalIzin
            });

            let rekapContent = generateTableContent(data.detail);
            document.getElementById('rekapContent').innerHTML = rekapContent;

            $('#rekapTable').hide();
            initializeDataTable();
        })
        .catch(error => console.error('Error:', error));
}

// Fungsi untuk menampilkan tabel detail saat tombol "Lihat Detil" diklik
document.getElementById('toggleTable').addEventListener('click', function() {
    $('#rekapTable').toggle();
});

// Fungsi untuk membuat konten tabel secara horizontal
function generateTableContent(data) {
    let content = `<table id="rekapTableContent" class="table table-bordered display">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Radius</th>
                <th>Istirahat</th>
                <th>Radius</th>
                <th>Pulang</th>
                <th>Radius</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>`;

    // Group data by date
    const groupedData = data.reduce((acc, absensi) => {
        if (!acc[absensi.date]) {
            acc[absensi.date] = { Masuk: '-', Istirahat: '-', Pulang: '-', MasukRadius: '-', IstirahatRadius: '-', PulangRadius: '-', status: '-' };
        }
        
        acc[absensi.date][absensi.type] = absensi.time;
        acc[absensi.date][`${absensi.type}Radius`] = absensi.is_in_radius ? 'Dalam Radius' : 'Luar Radius';

        // Set status based on "Masuk" type absensi only
        if (absensi.type === 'Masuk') {
            acc[absensi.date].status = absensi.status;
        }
        
        return acc;
    }, {});

    // Generate table rows
    for (const [date, absensi] of Object.entries(groupedData)) {
        content += `<tr>
            <td>${date}</td>
            <td>${absensi.Masuk}</td>
            <td><span style="color: ${absensi.MasukRadius === 'Dalam Radius' ? 'green' : 'red'};">${absensi.MasukRadius}</span></td>
            <td>${absensi.Istirahat}</td>
            <td><span style="color: ${absensi.IstirahatRadius === 'Dalam Radius' ? 'green' : 'red'};">${absensi.IstirahatRadius}</span></td>
            <td>${absensi.Pulang}</td>
            <td><span style="color: ${absensi.PulangRadius === 'Dalam Radius' ? 'green' : 'red'};">${absensi.PulangRadius}</span></td>
            <td><span style="color: ${absensi.status === 'Berhasil' ? 'green' : 'red'};">${absensi.status}</span></td>
        </tr>`;
    }

    content += `</tbody></table>`;
    return content;
}

// Inisialisasi DataTable
function initializeDataTable() {
    let tableElement = $('#rekapTableContent');

    if ($.fn.DataTable.isDataTable(tableElement)) {
        tableElement.DataTable().clear().destroy();
    }

    if (tableElement.find('tbody tr').length > 0) {
        tableElement.DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "paginate": {
                    "previous": "<",
                    "next": ">"
                }
            }
        });
    }
}

// Placeholder untuk chart kehadiran
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'bar', // Ganti jadi bar chart
    data: {
        labels: ['Hadir', 'Terlambat', 'Izin'], // Label untuk setiap kategori
        datasets: [{
            label: 'Jumlah',
            data: [0, 0, 0], // Data default (nanti di-update)
            backgroundColor: [
                '#36a2eb', // Warna untuk 'Hadir'
                '#ffcd56', // Warna untuk 'Terlambat'
                '#4bc0c0'  // Warna untuk 'Izin'
            ],
            borderColor: [
                '#2b8bc6', // Border warna untuk 'Hadir'
                '#e6b347', // Border warna untuk 'Terlambat'
                '#3e9d99'  // Border warna untuk 'Izin'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true, // Mulai dari 0 biar keliatan jelas
                title: {
                    display: true,
                    text: 'Jumlah Kehadiran', // Judul di sumbu Y
                    color: '#333'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Kategori', // Judul di sumbu X
                    color: '#333'
                }
            }
        },
        plugins: {
            legend: {
                display: false // Sembunyikan legend karena cuma satu dataset
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.raw;
                        return label;
                    }
                }
            }
        }
    }
});


// Script untuk menampilkan jam real-time di halaman beserta tanggal
function updateTime() {
    const currentDate = new Date();
    const currentTime = currentDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const currentDateString = currentDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    document.getElementById('currentTime').textContent = `ABSENSI HARI INI - ${currentTime} - ${currentDateString}`;
}
setInterval(updateTime, 1000);
updateTime();
</script>
@endsection


<!-- Chat and Broadcast Section at the Bottom -->
<div class="container mt-4" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <button id="open-chat-admin" class="btn btn-primary">Chat with Admin</button>
    <button id="open-broadcasts" class="btn btn-secondary">View Broadcasts</button>
</div>

<!-- Chat Modal for Employee -->
<div id="chat-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border: 1px solid #ccc; z-index: 1000;">
    <h5>Chat with Admin</h5>
    <div id="chat-messages" style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <!-- Chat messages will be loaded here -->
    </div>
    <textarea id="chat-message" class="form-control" placeholder="Type your message..."></textarea>
    <button id="send-message" class="btn btn-success mt-2">Send</button>
    <button id="close-chat-modal" class="btn btn-danger mt-2">Close</button>
</div>

<!-- Broadcast Modal for Employee -->
<div id="broadcast-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border: 1px solid #ccc; z-index: 1000;">
    <h5>Broadcast Messages</h5>
    <div id="broadcast-list" style="max-height: 200px; overflow-y: auto;">
        <!-- Broadcast messages will be loaded here -->
    </div>
    <button id="close-broadcast-modal" class="btn btn-danger mt-2">Close</button>
</div>

<script>
    // Toggle chat modal
    document.getElementById('open-chat-admin').addEventListener('click', function() {
        $('#chat-modal').fadeIn();
        loadChatMessages(); // Load chat messages with admin
    });

    document.getElementById('close-chat-modal').addEventListener('click', function() {
        $('#chat-modal').fadeOut();
    });

    // Toggle broadcast modal
    document.getElementById('open-broadcasts').addEventListener('click', function() {
        $('#broadcast-modal').fadeIn();
        loadBroadcasts(); // Load broadcast messages
    });

    document.getElementById('close-broadcast-modal').addEventListener('click', function() {
        $('#broadcast-modal').fadeOut();
    });

    // Send chat message to admin
    document.getElementById('send-message').addEventListener('click', function() {
        let message = document.getElementById('chat-message').value;
        $.ajax({
            url: '/api/chats/send',
            method: 'POST',
            data: {
                receiver_id: 1, // Assuming admin has an ID of 1
                message: message,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#chat-messages').append(`<p>${message}</p>`);
                document.getElementById('chat-message').value = '';
            },
            error: function(xhr) {
                alert('Failed to send message.');
            }
        });
    });

    // Load chat messages with admin
    function loadChatMessages() {
        $.ajax({
            url: '/api/chats/1', // Assuming admin has an ID of 1
            method: 'GET',
            success: function(messages) {
                let chatMessages = $('#chat-messages');
                chatMessages.empty();
                messages.forEach(message => {
                    chatMessages.append(`<p>${message.message}</p>`);
                });
            }
        });
    }

    // Load broadcast messages
    function loadBroadcasts() {
        $.ajax({
            url: '/api/broadcasts',
            method: 'GET',
            success: function(response) {
                let broadcastList = $('#broadcast-list');
                broadcastList.empty();
                response.forEach(broadcast => {
                    broadcastList.append(`<p>${broadcast.message} - <small>${new Date(broadcast.created_at).toLocaleString()}</small></p>`);
                });
            }
        });
    }
</script>
