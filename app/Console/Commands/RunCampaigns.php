<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Services\GoogleTrendsService;
use App\Services\GeminiService;
use App\Services\BloggerService;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RunCampaigns extends Command
{
    /**
     *
     * @var string
     */
    protected $signature = 'campaign:run';

    /**
     *
     * @var string
     */
    protected $description = 'Menjalankan bot auto post untuk mengecek jadwal campaign aktif';

    public function handle(GoogleTrendsService $trendsService, GeminiService $geminiService, BloggerService $bloggerService, ImageService $imageService)
    {
        $this->info('Memulai pengecekan sistem Bot Campaign...');

        $campaigns = Campaign::where('is_active', true)->get();

        if ($campaigns->isEmpty()) {
            $this->warn('Tidak ada campaign aktif saat ini.');
            return;
        }

        foreach ($campaigns as $campaign) {
            $this->line("-------------------------------------------------");
            $this->info("Mengecek Campaign: {$campaign->name}");

            if ($campaign->last_run_at) {
                $nextRun = $campaign->last_run_at->copy()->addMinutes($campaign->interval_minutes);
                
                if (now()->lessThan($nextRun)) {
                    $this->warn("=> Belum waktunya jalan. Jadwal berikutnya: {$nextRun->format('H:i')}");
                    continue;
                }
            }

            $selectedKeyword = '';
            $newsContext = '';

            if ($campaign->source_type === 'custom_keywords') {
                $queue = $campaign->keyword_queue;
                
                if (empty(trim((string)$queue))) {
                    $this->warn("=> Gagal: Antrean keyword manual sudah habis/kosong. Silakan isi lagi di dashboard.");
                    continue;
                }

                $this->info("=> Mode Manual (Evergreen SEO) Aktif!");

                $keywordsArray = explode("\n", str_replace("\r", "", trim($queue)));
                $selectedKeyword = array_shift($keywordsArray);
                $newsContext = "Tulis artikel mendalam berdasarkan keyword ini secara umum.";
                
                $campaign->update([
                    'keyword_queue' => implode("\n", $keywordsArray)
                ]);

            } else {
                $this->info("=> Waktunya eksekusi! Menarik data trend dari negara: {$campaign->geo_location}...");
                
                $trendsList = $trendsService->fetchDailyTrends($campaign->geo_location);

                if (empty($trendsList)) {
                    $this->error("=> Gagal menemukan keyword trending hari ini. Dilewati.");
                    continue;
                }

                $foundFreshKeyword = false;

                foreach ($trendsList as $item) {
                    $cacheKey = 'posted_trend_' . Str::slug($item['keyword']);
                    
                    if (!Cache::has($cacheKey)) {
                        $selectedKeyword = $item['keyword'];
                        $newsContext = $item['snippet'];
                        $foundFreshKeyword = true;
                        
                        Cache::put($cacheKey, true, now()->addDays(2)); 
                        break;
                    }
                }

                if (!$foundFreshKeyword) {
                    $this->warn("=> Semua topik trending hari ini SUDAH PERNAH diposting. Bot tidur lagi menunggu trend baru besok.");
                    continue;
                }
            }

            $this->info("=> KEYWORD TERPILIH: [ {$selectedKeyword} ]");
            $this->info("=> KONTEKS BERITA: " . ($newsContext ?: 'Tidak ada cuplikan spesifik'));

            $this->info("=> Mengirim perintah ke AI Gemini untuk menulis artikel...");
            
            $articleData = $geminiService->generateArticle($selectedKeyword, $campaign->name, $newsContext);

            if (!$articleData) {
                $this->error("=> AI Gemini gagal membuat artikel. Cek log error. Dilewati.");
                continue;
            }

            $this->info("=> ARTIKEL SELESAI DITULIS!");
            $this->info("=> Judul: " . $articleData['title']);

            $finalContent = $articleData['content'];
            
            if ($campaign->include_image) {
                $this->info("=> Opsi Gambar AKTIF. Mencari gambar ilustrasi dari Pixabay...");
                $imageUrl = $imageService->fetchImage($selectedKeyword);
                
                if ($imageUrl) {
                    $this->info("=> Gambar berhasil diproses via ImgBB! Menyisipkan ke dalam artikel...");
                    
                    $imageHtml = "<div style='text-align: center; margin-bottom: 20px;'>
                                    <img src='{$imageUrl}' alt='{$selectedKeyword}' title='{$selectedKeyword}' style='max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);' />
                                  </div>";
                                  
                    $finalContent = $imageHtml . "\n" . $finalContent;
                } else {
                    $this->warn("=> Gambar tidak ditemukan atau API Key belum diatur. Lanjut tanpa gambar.");
                }
            } else {
                $this->warn("=> Opsi Gambar DIMATIKAN. Bot akan memposting teks murni.");
            }

            $this->info("=> Sedang mempublikasikan artikel ke Blogger...");
            
            $isPublished = $bloggerService->publishPost(
                $campaign->blog, 
                $articleData['title'], 
                $finalContent, 
                $campaign->name,
                $articleData['description']
            );

            if ($isPublished) {
                $this->info("=> SUKSES! Artikel berhasil dipublish ke Blogger.");
                
                $campaign->update(['last_run_at' => now()]);
            } else {
                $this->error("=> GAGAL mempublish artikel. Cek file storage/logs/laravel.log untuk detailnya.");
            }
            
            $this->info("=> Campaign berhasil dieksekusi. Menunggu interval berikutnya.");
        }
        
        $this->line("-------------------------------------------------");
        $this->info('Pengecekan selesai.');
    }
}