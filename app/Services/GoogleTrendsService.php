<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTrendsService
{
    /**
     * 
     * @param string $geo Kode negara (misal: 'ID')
     * @return array Array berisi daftar ['keyword' => 'Topik', 'snippet' => 'Konteks Berita']
     */
    public function fetchDailyTrends(string $geo = 'ID'): array
    {
        $trendsData = [];

        try {
            $rssUrl = "https://trends.google.co.id/trending/rss?geo={$geo}";
            
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/rss+xml, application/xml, text/xml, */*',
                ])
                ->timeout(15)
                ->get($rssUrl);

            if ($response->successful()) {
                $xmlString = $response->body();
                
                if (preg_match_all('/<item>(.*?)<\/item>/is', $xmlString, $items)) {
                    foreach ($items[1] as $item) {
                        $cleanTitle = '';
                        if (preg_match('/<title>(.*?)<\/title>/is', $item, $titleMatch)) {
                            $cleanTitle = str_replace(['<![CDATA[', ']]>'], '', $titleMatch[1]);
                            $cleanTitle = trim(strip_tags($cleanTitle));
                        }

                        $cleanSnippet = '';
                        if (preg_match('/<ht:news_item_title>(.*?)<\/ht:news_item_title>/is', $item, $newsMatch) || 
                            preg_match('/<description>(.*?)<\/description>/is', $item, $newsMatch)) {
                            $cleanSnippet = str_replace(['<![CDATA[', ']]>'], '', $newsMatch[1]);
                            $cleanSnippet = trim(strip_tags($cleanSnippet));
                        }
                        
                        if (!empty($cleanTitle)) {
                            $trendsData[] = [
                                'keyword' => $cleanTitle,
                                'snippet' => $cleanSnippet
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Mesin 1 (RSS) Gagal: " . $e->getMessage());
        }

        try {
            $jsonUrl = "https://trends.google.co.id/trends/api/dailytrends?hl=id&tz=-420&geo={$geo}";
            
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ])
                ->timeout(15)
                ->get($jsonUrl);

            if ($response->successful()) {
                $body = $response->body();
                $cleanBody = preg_replace('/^\)\]\}\',\s*/', '', $body);
                $data = json_decode($cleanBody, true);

                if (isset($data['default']['trendingSearchesDays'])) {
                    foreach ($data['default']['trendingSearchesDays'] as $day) {
                        if (isset($day['trendingSearches'])) {
                            foreach ($day['trendingSearches'] as $search) {
                                if (isset($search['title']['query'])) {
                                    $keyword = (string) $search['title']['query'];
                                    
                                    $snippet = '';
                                    if (isset($search['articles'][0]['title'])) {
                                        $snippet = (string) $search['articles'][0]['title'];
                                    }

                                    $trendsData[] = [
                                        'keyword' => $keyword,
                                        'snippet' => $snippet
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Mesin 2 (JSON) Gagal: " . $e->getMessage());
        }

        $uniqueTrends = [];
        $seenKeywords = [];
        
        foreach ($trendsData as $item) {
            $key = strtolower(trim($item['keyword']));
            
            if (!in_array($key, $seenKeywords)) {
                $seenKeywords[] = $key;
                $uniqueTrends[] = $item;
            }
        }

        return $uniqueTrends;
    }
}