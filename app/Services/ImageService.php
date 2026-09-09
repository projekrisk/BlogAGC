<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageService
{
    public function fetchImage(string $keyword): ?string
    {
        $pixabayKey = env('PIXABAY_API_KEY');
        $imgbbKey = env('IMGBB_API_KEY');

        if (empty($pixabayKey) || empty($imgbbKey)) {
            Log::warning("API Key Pixabay atau ImgBB kosong. Gambar tidak diproses.");
            return null;
        }

        $searchQuery = implode(' ', array_slice(explode(' ', $keyword), 0, 2));
        $url = "https://pixabay.com/api/";

        try {
            $response = Http::withoutVerifying()->timeout(15)->get($url, [
                'key' => $pixabayKey,
                'q' => $searchQuery,
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'per_page' => 3
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['hits']) && count($data['hits']) > 0) {
                    $imageUrl = $data['hits'][0]['webformatURL'] ?? null;
                    
                    if ($imageUrl) {
                        $imageResponse = Http::withoutVerifying()->timeout(20)->get($imageUrl);
                        
                        if ($imageResponse->successful()) {
                            $base64Image = base64_encode($imageResponse->body());
                            
                            $uploadResponse = Http::withoutVerifying()->timeout(30)->asForm()->post("https://api.imgbb.com/1/upload", [
                                'key' => $imgbbKey,
                                'image' => $base64Image,
                                'name' => 'agc_' . time() . '_' . Str::slug($searchQuery),
                            ]);
                            
                            if ($uploadResponse->successful()) {
                                $uploadData = $uploadResponse->json();
                                
                                return $uploadData['data']['url'] ?? null;
                            } else {
                                Log::error("Gagal upload ke ImgBB: " . $uploadResponse->body());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengambil/memproses gambar: " . $e->getMessage());
        }

        return null;
    }
}