<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Dashboard') }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <style>
        /* Tambahin style buat cursor pointer */
        .employee-item {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="#">
                    {{ config('app.name', 'Admin Dashboard') }}
                </a>

                <div class="collapse navbar-collapse">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-2 bg-light border-right">
                    <div class="sidebar-header p-3">
                        <h3>Admin Menu</h3>
                    </div>
                    <ul class="list-unstyled components">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.dataAbsensi') }}">Data Absensi</a></li>
                        <li><a href="{{ route('admin.manajemenUsers') }}">Manajemen Users</a></li>
                        <li><a href="{{ route('admin.pengaturanSistem') }}">Pengaturan Sistem</a></li>
                        <li><a href="#" id="help-sidebar-toggle">Help Chat</a></li>
                        <button id="open-broadcast" class="btn btn-primary mt-3">Broadcast</button>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="col-md-10">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Sidebar Chat -->
        <div id="help-sidebar" style="display: none; position: fixed; right: 0; top: 0; width: 300px; height: 100vh; background-color: #f4f4f4; overflow-y: auto; border-left: 1px solid #ccc; z-index: 1000;">
            <div class="sidebar-header p-3">
                <h4>Help Chat</h4>
                <button id="close-help-sidebar" class="btn btn-danger btn-sm">Close</button>
            </div>

            <!-- Search Bar -->
            <div class="search-bar p-2">
                <input type="text" id="search-employee" class="form-control" placeholder="Cari Karyawan...">
            </div>

            <!-- List of Employees -->
            <ul id="employee-list" class="list-group">
                <!-- Populated by AJAX -->
            </ul>

            <!-- Chat Panel -->
            <div id="chat-panel" style="display: none;">
                <div id="chat-header">
                    <h5 id="chat-with">Chat dengan: <span></span></h5>
                </div>
                <div id="chat-messages" style="height: 400px; overflow-y: auto;">
                    <!-- Chat messages populated by AJAX -->
                </div>
                <div class="chat-input p-2">
                    <textarea id="chat-message" class="form-control" placeholder="Ketik pesan..."></textarea>
                    <button id="send-message" class="btn btn-primary mt-2">Kirim</button>
                </div>
            </div>
        </div>

        <!-- Modal buat Broadcast -->
        <div id="broadcast-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border: 1px solid #ccc; z-index: 1000;">
            <h5>Broadcast Message</h5>
            <textarea id="broadcast-message" class="form-control" placeholder="Tulis pesan broadcast..."></textarea>
            <button id="send-broadcast" class="btn btn-success mt-2">Kirim</button>
            <button id="close-broadcast" class="btn btn-danger mt-2">Batal</button>
            <hr>
            <h5>Pesan Broadcast Sebelumnya</h5>
            <div id="broadcast-list" style="max-height: 200px; overflow-y: auto;">
                <!-- Pesan broadcast akan diisi dengan AJAX -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & jQuery -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Toggle Help Sidebar
        document.getElementById('help-sidebar-toggle').addEventListener('click', function() {
            document.getElementById('help-sidebar').style.display = 'block';
            loadEmployeeList();
        });

        document.getElementById('close-help-sidebar').addEventListener('click', function() {
            document.getElementById('help-sidebar').style.display = 'none';
        });

        // Load Employee List
        function loadEmployeeList() {
            $.ajax({
                url: '/api/employees', // Endpoint API buat ambil daftar karyawan
                method: 'GET',
                success: function(data) {
                    let employeeList = $('#employee-list');
                    employeeList.empty();
                    data.forEach(employee => {
                        employeeList.append(`
                            <li class="list-group-item employee-item" data-id="${employee.id}" data-name="${employee.name}">
                                ${employee.name}
                            </li>
                        `);
                    });

                    // Attach click event to open chat
                    $('.employee-item').click(function() {
                        let employeeId = $(this).data('id');
                        let employeeName = $(this).data('name'); // Ambil nama karyawan
                        openChatPanel(employeeId, employeeName);
                    });
                }
            });
        }

        // Open Chat Panel
        function openChatPanel(employeeId, employeeName) {
            $('#chat-panel').show();
            $('#chat-with span').text(employeeName); // Tampilkan nama karyawan

            // Fetch previous messages
            $.ajax({
                url: `/api/chats/${employeeId}`, // Replace with actual route to fetch chats
                method: 'GET',
                success: function(messages) {
                    let chatMessages = $('#chat-messages');
                    chatMessages.empty();
                    messages.forEach(message => {
                        chatMessages.append(`<p>${message.message}</p>`);
                    });
                }
            });

            // Send new message
            $('#send-message').off('click').on('click', function() {
                let message = $('#chat-message').val();
                $.ajax({
                    url: '/api/chats/send',
                    method: 'POST',
                    data: {
                        receiver_id: employeeId,
                        message: message,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#chat-messages').append(`<p>${message}</p>`);
                        $('#chat-message').val('');
                    }
                });
            });
        }

        // Toggle broadcast modal
        document.getElementById('open-broadcast').addEventListener('click', function() {
            document.getElementById('broadcast-modal').style.display = 'block';
            loadBroadcasts(); // Load pesan broadcast sebelumnya
        });

        document.getElementById('close-broadcast').addEventListener('click', function() {
            document.getElementById('broadcast-modal').style.display = 'none';
        });

        // Send broadcast message
        document.getElementById('send-broadcast').addEventListener('click', function() {
            let message = document.getElementById('broadcast-message').value;
            $.ajax({
                url: '/api/broadcasts/send',
                method: 'POST',
                data: {
                    message: message,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Broadcast berhasil dikirim!');
                    document.getElementById('broadcast-message').value = '';
                    loadBroadcasts(); // Refresh daftar pesan broadcast
                },
                error: function(xhr) {
                    alert('Gagal mengirim broadcast. Pastikan pesan tidak kosong.');
                }
            });
        });

        // Load previous broadcasts
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
</body>
</html>
