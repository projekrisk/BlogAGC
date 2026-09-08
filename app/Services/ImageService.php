<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageService
{
    public function fetchImage(string $keyword): ?string
    {
        $apiKey = env('PIXABAY_API_KEY');

        if (empty($apiKey)) {
            Log::warning("Pixabay API Key kosong. Gambar tidak akan ditambahkan.");
            return null;
        }

        $searchQuery = implode(' ', array_slice(explode(' ', $keyword), 0, 2));
        $url = "https://pixabay.com/api/";

        try {
            $response = Http::withoutVerifying()->timeout(15)->get($url, [
                'key' => $apiKey,
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
                            $imageBody = $imageResponse->body();
                            $mimeType = $imageResponse->header('Content-Type') ?? 'image/jpeg';
                            
                            $base64 = base64_encode($imageBody);
                            return "data:{$mimeType};base64,{$base64}";
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengambil/memproses gambar Pixabay: " . $e->getMessage());
        }

        return null;
    }
}