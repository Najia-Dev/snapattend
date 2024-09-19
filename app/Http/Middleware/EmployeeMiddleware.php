<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'karyawan') {
            return $next($request); // Izinkan akses jika karyawan
        }

        return redirect('/'); // Redirect ke halaman utama jika bukan karyawan
    }
}
