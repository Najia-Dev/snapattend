<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SnapAttend</title>

    <!-- Styles -->
    <style>
        /* Background blur */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
            filter: blur(8px); /* Blur efek pada background */
        }

        /* Kontainer utama */
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.6); /* Putih transparan */
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(0); /* Tanpa blur di kotak */
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            position: relative;
            z-index: 1; /* Agar kotak dan teks tampil di depan background */
        }

        /* Styling logo */
        .logo {
            margin-bottom: 20px;
        }

        /* Warna teks Welcome jadi hitam */
        h1 {
            color: #000; /* Warna hitam */
        }

        /* Warna teks untuk link (LOGIN, REGISTER) jadi biru */
        a {
            text-decoration: none;
            color: #007bff; /* Warna biru */
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Styling footer teks jadi hitam */
        .footer {
            margin-top: 20px;
            color: #000; /* Warna hitam */
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Background dengan efek blur -->
    <div class="background"></div>

    <!-- Kontainer konten utama -->
    <div class="container">
        <!-- Logo -->
        <img src="{{ asset('logo.png') }}" alt="Logo SnapAttend" class="logo" style="width: 150px; height: auto;">

        <!-- Teks utama -->
        <h1>Selamat Datang di SnapAttend</h1>

        <!-- Login/Register Links -->
        @if (Route::has('login'))
            <nav>
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">LOGIN</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">REGISTER</a>
                    @endif
                @endauth
            </nav>
        @endif

        <!-- Footer -->
        <div class="footer">
            Copyright 1092024 created by Najia-Dev | SnapAttend v.0.1
        </div>
    </div>
</body>
</html>
