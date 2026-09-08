<?php

namespace App\Services;

use App\Models\Blog;
use Google\Client as GoogleClient;
use Google\Service\Blogger as GoogleServiceBlogger;
use Google\Service\Blogger\Post as BloggerPost;
use Illuminate\Support\Facades\Log;

class BloggerService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->addScope('https://www.googleapis.com/auth/blogger');
        $this->client->setAccessType('offline');

        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $this->client->setHttpClient($guzzleClient);
    }

    /**
     */
    public function publishPost(Blog $blog, string $title, string $content, string $label = null, string $searchDescription = null): bool
    {
        if (empty($blog->access_token) || empty($blog->blogger_blog_id)) {
            Log::error("Blog {$blog->name} belum dihubungkan ke Google ATAU ID Blog kosong.");
            return false;
        }

        $token = [
            'access_token' => $blog->access_token,
            'refresh_token' => $blog->refresh_token,
            'expires_in' => $blog->token_expires_at ? $blog->token_expires_at->diffInSeconds(now()) : 3600,
        ];

        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                
                if (!isset($newToken['error'])) {
                    $blog->update([
                        'access_token' => $newToken['access_token'],
                        'token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                } else {
                    Log::error("Gagal refresh token Google: " . json_encode($newToken));
                    return false;
                }
            } else {
                Log::error("Tidak ada refresh token untuk blog {$blog->name}. Harus relogin dari dashboard.");
                return false;
            }
        }

        try {
            $bloggerService = new GoogleServiceBlogger($this->client);
            $post = new BloggerPost();
            
            $post->setTitle($title);
            $post->setContent($content);
            
            if ($label) {
                $post->setLabels([$label]);
            }
            
            if ($searchDescription) {
                $post->setCustomMetaData($searchDescription);
            }

            $bloggerService->posts->insert($blog->blogger_blog_id, $post, ['isDraft' => false]);
            
            return true;

        } catch (\Exception $e) {
            Log::error("Gagal memposting ke Blogger: " . $e->getMessage());
            return false;
        }
    }
}