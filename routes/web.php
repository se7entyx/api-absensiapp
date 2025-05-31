<?php

use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\KantorController;
use App\Http\Controllers\LiburController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login');
});

// Default routes
Route::get('/', function () {
    if (Auth::check()) {
        // User is authenticated
        return to_route('dashboard');
    } else {
        // User is not authenticated
        return to_route('login');
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('password.update');

    Route::middleware('check.profile')->group(function () {
        // Route::get('/izin/rekap', [IzinController::class, 'rekap'])->name('izin.rekap');
        // Route::get('/cuti/rekap', [CutiController::class, 'rekap'])->name('cuti.rekap');

        Route::get('/izin/new', [IzinController::class, 'index'])->name('izin.index');
        Route::post('/izin/store', [IzinController::class, 'store'])->name('izin.store');
        Route::get('/izin/ongoing', [IzinController::class, 'onGoing'])->name('izin.ongoing');
        Route::get('/izin/update/{id}', [IzinController::class, 'edit'])->name('izin.edit');
        Route::put('/izin/update/{id}', [IzinController::class, 'update'])->name('izin.update');
        Route::delete('/izin/destroy/{id}', [IzinController::class, 'destroy'])->name('izin.destroy');
        Route::get('/izin/approve/{id}', [IzinController::class, 'approveIndex'])->name('izin.approveIndex');
        Route::put('/izin/approve/{id}', [IzinController::class, 'approve'])->name('izin.approve');
        Route::put('/izin/reject/{id}', [IzinController::class, 'reject'])->name('izin.reject');
        Route::get('/izin/print/{id}', [IzinController::class, 'print'])->name('izin.print');

        Route::get('/cuti/new', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/hitung-hari-kerja', [CutiController::class, 'hitungHariKerja'])->name('hitung.hari.kerja');
        Route::post('/cuti/store', [CutiController::class, 'store'])->name('cuti.store');
        Route::get('/cuti/ongoing', [CutiController::class, 'onGoing'])->name('cuti.ongoing');
        Route::get('/cuti/update/{id}', [CutiController::class, 'edit'])->name('cuti.edit');
        Route::put('/cuti/update/{id}', [CutiController::class, 'update'])->name('cuti.update');
        Route::delete('/cuti/destroy/{id}', [CutiController::class, 'destroy'])->name('cuti.destroy');
        Route::get('/cuti/approve/{id}', [CutiController::class, 'approveIndex'])->name('cuti.approveIndex');
        Route::put('/cuti/approve/{id}', [CutiController::class, 'approve'])->name('cuti.approve');
        Route::put('/cuti/reject/{id}', [CutiController::class, 'reject'])->name('cuti.reject');
        Route::get('/cuti/print/{id}', [CutiController::class, 'print'])->name('cuti.print');
        Route::get('/presensi/dinas', [PresensiController::class, 'dinas'])->name('presensi.dinas');
        Route::post('/presensi/verifikasi', [PresensiController::class, 'verifikasi'])->name('presensi.verifikasi');
        Route::get('/presensi/my', [PresensiController::class, 'my'])->name('presensi.my');
        Route::get('/presensi/export', [PresensiController::class, 'export'])->name('presensi.export');

        Route::middleware('check.hrd')->group(function () {
            Route::get('/cuti/rekap', [CutiController::class, 'rekap'])->name('cuti.rekap');
            Route::get('/izin/rekap', [IzinController::class, 'rekap'])->name('izin.rekap');
            Route::get('/presensi/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
            Route::put('/presensi/update/{id}', [PresensiController::class, 'update'])->name('presensi.update');
        });

        Route::middleware('check.admin')->group(function () {
            Route::get('/master/users', [UserController::class, 'index'])->name('user.index');
            Route::post('/master/users/store', [UserController::class, 'store'])->name('user.store');
            Route::post('/master/users/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/master/users/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
            Route::get('/read-uid', [UserController::class, 'read_uid'])->name('read-uid');
            Route::get('/master/departments', [DepartmentController::class, 'index'])->name('department.index');
            Route::post('/master/departments/store', [DepartmentController::class, 'store'])->name('department.store');
            Route::post('/master/departments/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('/master/departments/destroy/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
            Route::get('/master/libur', [LiburController::class, 'index'])->name('libur.index');
            Route::post('/master/libur/store', [LiburController::class, 'store'])->name('libur.store');
            Route::post('/master/libur/update/{id}', [LiburController::class, 'update'])->name('libur.update');
            Route::delete('/master/libur/destroy/{id}', [LiburController::class, 'destroy'])->name('libur.destroy');
            Route::get('/master/kantor', [KantorController::class, 'index'])->name('kantor.index');
            Route::post('/master/kantor/store', [KantorController::class, 'store'])->name('kantor.store');
            Route::put('/master/kantor/update/{id}', [KantorController::class, 'update'])->name('kantor.update');
            Route::delete('/master/kantor/destroy/{id}', [KantorController::class, 'destroy'])->name('kantor.destroy');
        });
    });
});
// Route::post('/login', [AuthController::class, 'apiLogin']);
