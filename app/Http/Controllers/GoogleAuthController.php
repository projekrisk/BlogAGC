<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\Blog;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    private GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        
        $this->client->addScope('https://www.googleapis.com/auth/blogger');
        
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $this->client->setHttpClient($guzzleClient);
    }

    public function redirect(Request $request)
    {
        $blogId = $request->query('blog_id');
        
        if (!$blogId) {
            return redirect('/admin/blogs')->with('error', 'Pilih blog terlebih dahulu!');
        }

        session(['auth_blog_id' => $blogId]);
        
        $authUrl = $this->client->createAuthUrl();
        
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/admin/blogs');
        }

        if ($request->has('code')) {
            $token = $this->client->fetchAccessTokenWithAuthCode($request->code);

            if (!isset($token['error'])) {
                $blogId = session('auth_blog_id');
                
                if ($blogId) {
                    $blog = Blog::find($blogId);
                    if ($blog) {
                        $blog->update([
                            'access_token' => $token['access_token'] ?? null,
                            'refresh_token' => $token['refresh_token'] ?? null,
                            'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds($token['expires_in']) : null,
                        ]);
                        
                        session()->forget('auth_blog_id');

                        return redirect('/admin/blogs');
                    }
                }
            } else {
                Log::error("Google Auth Error: " . json_encode($token));
            }
        }
        
        return redirect('/admin/blogs');
    }
}