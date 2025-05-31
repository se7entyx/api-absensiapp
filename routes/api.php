<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'apiLogin']);
Route::get('/test', [AuthController::class, 'test']);
Route::get('/get/users', [UserController::class, 'getUsers']);
Route::post('/users/store', [UserController::class, 'store']);
Route::post('/users/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/users/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'apiLogout']);
Route::get('/presensi/{uid}', [PresensiController::class, 'readUid']);
Route::post('/verifikasi',[PresensiController::class,'verifyFace']);
Route::post('/dinas/verifikasi',[PresensiController::class,'verifikasi']);
// Route::post('/logout', [AuthController::class, 'apiLogout']);