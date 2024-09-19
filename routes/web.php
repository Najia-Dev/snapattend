<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbsensiDaruratController;
use App\Http\Controllers\IzinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes untuk aplikasi Laravel, mengatur akses ke halaman dashboard, absensi,
| manajemen users, pengaturan sistem, rekap absensi, dan absensi darurat & izin.
|
*/

// Route untuk halaman utama atau landing page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes, Laravel default
Auth::routes();

// Ensure there is a route for home
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Group route dengan middleware auth untuk mengamankan route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {

    // Route untuk halaman dashboard admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Route untuk halaman absensi (formulir absensi dan menyimpan absensi)
    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
    
    // Route untuk ekspor data absensi
    Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');

    // Route untuk halaman edit profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Route untuk update foto profil
    Route::post('/profile/update_photo', [ProfileController::class, 'updatePhoto'])->name('profile.update_photo');

    // Route untuk halaman Help
    Route::get('/help', function () {
        return view('help');  // Pastikan ada file 'help.blade.php' di folder 'resources/views'
    })->name('help');

    // Route untuk rekap data absensi (mingguan, bulanan, tahunan)
    Route::get('/rekap/mingguan', [RekapAbsensiController::class, 'rekapMingguan'])->name('rekap.mingguan');
    Route::get('/rekap/bulanan', [RekapAbsensiController::class, 'rekapBulanan'])->name('rekap.bulanan');
    Route::get('/rekap/tahunan', [RekapAbsensiController::class, 'rekapTahunan'])->name('rekap.tahunan');

    // Route untuk halaman data absensi dengan filter bulan dan tahun
    Route::get('/admin/data-absensi', [AdminController::class, 'dataAbsensi'])->name('admin.dataAbsensi');

    // Route untuk halaman Manajemen Users
    Route::get('/admin/manajemen-users', [AdminController::class, 'manajemenUsers'])->name('admin.manajemenUsers');
    Route::post('/admin/manajemen-users/store', [AdminController::class, 'storeUser'])->name('admin.storeUser');  // Metode POST untuk menambah user baru
    Route::get('/admin/manajemen-users/edit/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');
    Route::post('/admin/manajemen-users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::delete('/admin/manajemen-users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // Route untuk halaman Pengaturan Sistem
    Route::get('/admin/pengaturan-sistem', [AdminController::class, 'showSettings'])->name('admin.pengaturanSistem');
    Route::post('/admin/pengaturan-sistem', [AdminController::class, 'updateSettings'])->name('admin.updateSettings');

    // Route untuk export data absensi ke Excel
    Route::get('/admin/export-excel', [AdminController::class, 'exportExcel'])->name('admin.exportAbsensi');

    // Tambahkan route untuk Absensi Darurat dan Izin
    Route::post('/absensi_darurat', [AbsensiDaruratController::class, 'store'])->name('absensi_darurat.store');
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
});

// Rute tes sederhana untuk memverifikasi bahwa Laravel dapat memuat rute dengan benar
Route::get('/admin/test', function () {
    return 'Admin Route is working!';
});

use App\Http\Controllers\ShiftFeature\ShiftFeatureController;

Route::get('/shifts', [ShiftFeatureController::class, 'index'])->name('shifts.index');
Route::get('/shifts/create', [ShiftFeatureController::class, 'create'])->name('shifts.create');
Route::post('/shifts', [ShiftFeatureController::class, 'store'])->name('shifts.store');

use App\Http\Controllers\ChatController;

Route::get('/api/employees', [ChatController::class, 'getEmployees'])->name('api.employees');
Route::post('/api/chats/send', [ChatController::class, 'sendMessage'])->name('api.chats.send');
Route::get('/api/chats/{employeeId}', [ChatController::class, 'fetchMessages'])->name('api.chats.fetch');

use App\Http\Controllers\BroadcastController;

Route::post('/api/broadcasts/send', [BroadcastController::class, 'sendBroadcast'])->name('api.broadcasts.send');
Route::get('/api/broadcasts', [BroadcastController::class, 'fetchBroadcasts'])->name('api.broadcasts.fetch');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/api/employees', [ChatController::class, 'getEmployees'])->name('api.employees');
    Route::post('/api/chats/send', [ChatController::class, 'sendMessage'])->name('api.chats.send');
    Route::get('/api/chats/{employeeId}', [ChatController::class, 'fetchMessages'])->name('api.chats.fetch');
    Route::post('/api/broadcasts/send', [BroadcastController::class, 'sendBroadcast'])->name('api.broadcasts.send');
    Route::get('/api/broadcasts', [BroadcastController::class, 'fetchBroadcasts'])->name('api.broadcasts.fetch');
});

