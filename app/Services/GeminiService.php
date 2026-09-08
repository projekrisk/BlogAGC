<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key={$this->apiKey}";
    }

    /**
     * 
     * @param string $keyword Keyword dari Google Trends
     * @param string $campaignName Nama campaign untuk konteks tambahan
     * @param string $contextSnippet Konteks berita spesifik agar AI tidak halusinasi
     * @return array Array berisi ['title' => 'Judul', 'content' => 'Isi HTML']
     */
    public function generateArticle(string $keyword, string $campaignName, string $contextSnippet = ''): ?array
    {
        if (empty($this->apiKey)) {
            Log::error("Gemini API Key belum diatur di file .env");
            return null;
        }

        // PROMPT
        $prompt = "Kamu adalah jurnalis dan penulis blog SEO profesional. 
        Tulis artikel berita/blog SEO yang komprehensif tentang topik utama: '{$keyword}'. 
        
        PENTING - KONTEKS BERITA HARI INI: '{$contextSnippet}'. 
        (WAJIB jadikan konteks berita tersebut sebagai sudut pandang utama tulisanmu agar artikelnya akurat dengan kejadian sebenarnya hari ini).
        
        ATURAN PENULISAN:
        0. WAJIB TULIS 100% DALAM BAHASA INDONESIA YANG BAIK, MENARIK, DAN NATURAL. Jika keyword atau konteks berita di atas menggunakan bahasa Inggris, TERJEMAHKAN dan bahas dalam Bahasa Indonesia!
        1. Buat Judul Artikel maksimal 70 karakter yang *Clickbait* tapi jujur (Tulis tanpa tag HTML, hanya teks biasa).
        2. Tulis artikel informatif sekitar 600 - 800 kata.
        3. DILARANG KERAS MENGGUNAKAN TAG <h1> DI DALAM KONTEN ARTIKEL! 
        4. WAJIB mulai baris pertama artikel langsung dengan PARAGRAF PEMBUKA (<p>) yang memancing rasa penasaran pembaca. JANGAN PERNAH meletakkan tag <h2> di awal artikel sebelum paragraf pembuka!
        5. Setelah paragraf pembuka, baru gunakan subjudul utama dengan <h2>, dan sub-poin dengan <h3> secara terstruktur.
        6. Rancang 3 pertanyaan FAQ beserta jawabannya secara internal (dalam pikiranmu), TAPI JANGAN TAMPILKAN teks FAQ tersebut di dalam HTML artikel.
        7. WAJIB buatkan Schema Markup SEO tipe FAQPage (format JSON-LD) berdasarkan 3 FAQ rahasia tersebut. Letakkan kode valid JSON-LD di dalam tag <script type=\"application/ld+json\"> pada baris paling bawah tulisan.
        8. Buat DESKRIPSI PENCARIAN (Meta Description) yang menarik, maksimal 150 karakter (tanpa tanda kutip).
        9. Format hasil tulisan DALAM BENTUK HTML RAW (Hanya boleh menggunakan tag <h2>, <h3>, <p>, <ul>, <li>, <strong>). 
        10. JANGAN gunakan tag <html>, <head>, atau <body>. JANGAN gunakan markdown block.
        11. Berikan pemisah yang jelas dengan format ini:
           JUDUL_ARTIKEL: [Isi Judul Disini]
           DESKRIPSI_PENCARIAN: [Isi Deskripsi Disini]
           KONTEN_ARTIKEL: [Isi HTML Disini, termasuk tag <script> Schema di akhir]";

        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->retry(3, 5000)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error("Gagal terhubung ke Gemini API: " . $response->body());
                return null;
            }

            $result = $response->json();
            $generatedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($generatedText)) {
                Log::error("Respon Gemini kosong.");
                return null;
            }

            $title = '';
            $description = '';
            $content = '';

            if (preg_match('/JUDUL_ARTIKEL:\s*(.*?)\n/s', $generatedText, $titleMatches)) {
                $title = trim($titleMatches[1]);
            }
            
            if (preg_match('/DESKRIPSI_PENCARIAN:\s*(.*?)\n/s', $generatedText, $descMatches)) {
                $description = trim($descMatches[1]);
            }

            if (preg_match('/KONTEN_ARTIKEL:\s*(.*)/s', $generatedText, $contentMatches)) {
                $content = trim($contentMatches[1]);
            }

            if (empty($title) || empty($content)) {
                $title = "Artikel tentang " . $keyword;
                $description = "Baca artikel selengkapnya tentang " . $keyword . " di sini.";
                $content = $generatedText;
            }

            $content = str_replace(['```html', '```'], '', $content);
            $content = trim($content);

            return [
                'title' => $title,
                'description' => $description,
                'content' => $content,
            ];

        } catch (\Exception $e) {
            Log::error("Error saat generate artikel: " . $e->getMessage());
            return null;
        }
    }
}