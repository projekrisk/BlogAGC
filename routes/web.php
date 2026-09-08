<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::get('/reset-hosting', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('filament:optimize-clear');
    return 'Berhasil! Cache Hosting telah disapu bersih. Silakan akses kembali halaman login admin.';
});