@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<div id="main-wrapper" class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="mb-5">
                                <h3 class="h4 font-weight-bold text-theme" style="color: #007bff;">LOGIN</h3>
                                </div>

                                <h6 class="h5 mb-0">Selamat Datang!</h6>
                                <p class="text-muted mt-2 mb-5">Masukan alamat email dan password untuk melakukan absensi.</p>

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">Alamat email</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-5">
                                        <label for="password">Password</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-theme">Login</button>

                                    @if (Route::has('password.request'))
                                        <a class="forgot-link float-right text-primary" href="{{ route('password.request') }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-6 d-none d-lg-inline-block">
                            <div class="account-block rounded-right">
                                <div class="overlay rounded-right"></div>
                                <div class="account-testimonial text-center d-flex flex-column justify-content-end align-items-center">
                                    <h4 class="text-white mb-4">Ini adalah SNAPATTEND</h4>
                                    <p class="lead text-white">"Aplikasi absensi foto wajah yang didukung radius jarak dan rentang waktu absensi."</p>
                                    <p class="text-white">- Najia Developer -</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-muted text-center mt-3 mb-0">Tidak punya akun? 
                <a href="{{ route('register') }}" class="text-primary ml-1">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
