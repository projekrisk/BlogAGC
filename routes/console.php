<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda mendefinisikan rute berbasis konsol (terminal) atau
| mengatur jadwal eksekusi otomatis (Cron Jobs).
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mendaftarkan Bot Campaign kita agar dicek setiap 1 menit.
// Jangan khawatir, bot tidak akan memposting setiap menit. 
// Bot hanya akan "mengecek" apakah interval waktu (misal: 120 menit) di database sudah terpenuhi.
Schedule::command('campaign:run')
    ->everyMinute()
    ->withoutOverlapping() // Mencegah bot berjalan ganda jika proses sebelumnya belum selesai
    ->appendOutputTo(storage_path('logs/bot-campaign.log')); // Menyimpan catatan aktivitas bot ke file log