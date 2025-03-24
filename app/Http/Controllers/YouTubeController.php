<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\YouTubeChannel;
use Google\Client as GoogleClient;
use Google\Service\YouTube;

class YouTubeController extends Controller
{
    // Redirect to YouTube OAuth
    public function redirect()
    {
        return Socialite::driver('youtube')
            ->scopes([
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.force-ssl'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    // Handle YouTube OAuth callback
    public function callback(Request $request)
    {
        $user = auth()->user();
        $youtubeUser = Socialite::driver('youtube')->user();

        // Initialize Google Client
        $client = new GoogleClient();
        $client->setAccessToken($youtubeUser->token);

        // Get YouTube service
        $youtube = new YouTube($client);

        try {
            // Get channels for authenticated user
            $channelsResponse = $youtube->channels->listChannels('snippet', ['mine' => true]);
            
            foreach ($channelsResponse->getItems() as $channel) {
                // Check if channel has live streaming enabled
                $canStream = false;
                try {
                    $broadcastsResponse = $youtube->liveBroadcasts->listLiveBroadcasts('id,snippet', [
                        'broadcastStatus' => 'all',
                        'mine' => true
                    ]);
                    $canStream = count($broadcastsResponse->getItems()) > 0;
                } catch (\Exception $e) {
                    // Channel doesn't have live streaming enabled
                }

                YouTubeChannel::updateOrCreate(
                    ['user_id' => $user->id, 'channel_id' => $channel->getId()],
                    [
                        'title' => $channel->getSnippet()->getTitle(),
                        'thumbnail_url' => $channel->getSnippet()->getThumbnails()->getDefault()->getUrl(),
                        'access_token' => $youtubeUser->token,
                        'refresh_token' => $youtubeUser->refreshToken,
                        'expires_at' => now()->addSeconds($youtubeUser->expiresIn),
                        'can_stream' => $canStream
                    ]
                );
            }

            return redirect()->route('youtube.channels')->with('success', 'YouTube account connected successfully!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Failed to fetch YouTube channels: ' . $e->getMessage());
        }
    }

    // List user's YouTube channels
    public function channels()
    {
        $channels = auth()->user()->youtubeChannels;
        return view('youtube.channels', compact('channels'));
    }
}