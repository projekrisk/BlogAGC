<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::get('/reset-hosting', function () {
    Artisan::call('optimize:clear');
    Artisan::call('filament:optimize-clear');
    return 'Berhasil! Cache Hosting telah disapu bersih. Silakan akses kembali halaman login admin.';
});

Route::get('/cron-bot-agc', function () {
    set_time_limit(120); 
    
    Artisan::call('schedule:run');
    
    $output = Artisan::output();
    
    return nl2br("Sistem Bot selesai dipicu pada: " . now() . "\n\nLog Eksekusi:\n" . $output);
});