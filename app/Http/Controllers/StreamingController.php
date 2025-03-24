<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YouTubeChannel;
use App\Models\FacebookAccount;
use Google\Client as GoogleClient;
use Google\Service\YouTube;

class StreamingController extends Controller
{
    // Generate YouTube stream credentials
    public function generateYouTubeStream(Request $request, $channelId)
    {
        $channel = YouTubeChannel::where('user_id', auth()->id())
            ->where('id', $channelId)
            ->where('can_stream', true)
            ->firstOrFail();

        try {
            $client = new GoogleClient();
            $client->setAccessToken($channel->access_token);
            
            if ($client->isAccessTokenExpired()) {
                $client->refreshToken($channel->refresh_token);
                $newToken = $client->getAccessToken();
                $channel->update([
                    'access_token' => $newToken['access_token'],
                    'expires_at' => now()->addSeconds($newToken['expires_in'])
                ]);
            }

            $youtube = new YouTube($client);
            
            // Create a live broadcast
            $broadcastSnippet = new YouTube\LiveBroadcastSnippet();
            $broadcastSnippet->setTitle('My Live Stream');
            $broadcastSnippet->setScheduledStartTime(date('c', strtotime('+1 minute')));
            
            $broadcastStatus = new YouTube\LiveBroadcastStatus();
            $broadcastStatus->setPrivacyStatus('private');
            
            $broadcastInsert = new YouTube\LiveBroadcast();
            $broadcastInsert->setSnippet($broadcastSnippet);
            $broadcastInsert->setStatus($broadcastStatus);
            $broadcastInsert->setKind('youtube#liveBroadcast');
            
            $broadcastResponse = $youtube->liveBroadcasts->insert('snippet,status', $broadcastInsert);
            
            // Create a live stream
            $streamSnippet = new YouTube\LiveStreamSnippet();
            $streamSnippet->setTitle('My Stream');
            
            $cdn = new YouTube\CdnSettings();
            $cdn->setFormat("1080p");
            $cdn->setIngestionType('rtmp');
            
            $streamInsert = new YouTube\LiveStream();
            $streamInsert->setSnippet($streamSnippet);
            $streamInsert->setCdn($cdn);
            $streamInsert->setKind('youtube#liveStream');
            
            $streamResponse = $youtube->liveStreams->insert('snippet,cdn', $streamInsert);
            
            // Bind the broadcast and stream
            $bindResponse = $youtube->liveBroadcasts->bind(
                $broadcastResponse->getId(),
                'id,contentDetails',
                ['streamId' => $streamResponse->getId()]
            );
            
            $streamData = [
                'rtmp_url' => $streamResponse->getCdn()->getIngestionInfo()->getIngestionAddress(),
                'stream_key' => $streamResponse->getCdn()->getIngestionInfo()->getStreamName(),
                'broadcast_id' => $broadcastResponse->getId(),
                'stream_id' => $streamResponse->getId(),
                'watch_url' => 'https://youtube.com/watch?v='.$broadcastResponse->getId()
            ];
            
            return view('streaming.youtube', compact('streamData'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate stream: ' . $e->getMessage());
        }
    }

    // Generate Facebook stream credentials
    public function generateFacebookStream(Request $request, $accountId)
    {
        $account = FacebookAccount::where('user_id', auth()->id())
            ->where('id', $accountId)
            ->where('can_stream', true)
            ->firstOrFail();

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://graph.facebook.com/v15.0/'.$account->account_id.'/live_videos', [
                'query' => [
                    'access_token' => $account->access_token,
                    'status' => 'LIVE_NOW',
                    'title' => 'My Live Stream'
                ]
            ]);
            
            $streamData = json_decode($response->getBody(), true);
            
            return view('streaming.facebook', [
                'rtmp_url' => $streamData['stream_url'] ?? null,
                'stream_key' => $streamData['secure_stream_url'] ?? null,
                'stream_id' => $streamData['id'] ?? null,
                'watch_url' => $streamData['permalink_url'] ?? null
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate stream: ' . $e->getMessage());
        }
    }
}