<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\FacebookAccount;

class FacebookController extends Controller
{
    // Redirect to Facebook OAuth
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'pages_show_list',
                'pages_read_engagement',
                'groups_access_member_info',
                'publish_video'
            ])
            ->redirect();
    }

    // Handle Facebook OAuth callback
    public function callback(Request $request)
    {
        $user = auth()->user();
        $facebookUser = Socialite::driver('facebook')->user();

        try {
            // Get user's profile
            $this->saveFacebookAccount($user, $facebookUser, 'profile', $facebookUser->id, $facebookUser->name, $facebookUser->avatar);

            // Get pages
            $pages = $this->getFacebookData($facebookUser->token, '/me/accounts');
            foreach ($pages['data'] ?? [] as $page) {
                $this->saveFacebookAccount($user, $facebookUser, 'page', $page['id'], $page['name'], null, $page['access_token']);
            }

            // Get groups
            $groups = $this->getFacebookData($facebookUser->token, '/me/groups');
            foreach ($groups['data'] ?? [] as $group) {
                $this->saveFacebookAccount($user, $facebookUser, 'group', $group['id'], $group['name'], null);
            }

            return redirect()->route('facebook.accounts')->with('success', 'Facebook account connected successfully!');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Failed to fetch Facebook data: ' . $e->getMessage());
        }
    }

    // Helper to save Facebook account
    private function saveFacebookAccount($user, $facebookUser, $type, $accountId, $name, $avatar = null, $accessToken = null)
    {
        // Check if this account can stream
        $canStream = false;
        if ($type === 'page' || $type === 'profile') {
            try {
                $streamTest = $this->getFacebookData($accessToken ?? $facebookUser->token, "/$accountId/live_videos", 'POST');
                $canStream = isset($streamTest['id']);
            } catch (\Exception $e) {
                // Account can't stream
            }
        }

        FacebookAccount::updateOrCreate(
            ['user_id' => $user->id, 'account_id' => $accountId],
            [
                'name' => $name,
                'type' => $type,
                'avatar_url' => $avatar ?? 'https://graph.facebook.com/'.$accountId.'/picture?type=square',
                'access_token' => $accessToken ?? $facebookUser->token,
                'can_stream' => $canStream
            ]
        );
    }

    // Helper to make Facebook API requests
    private function getFacebookData($token, $endpoint, $method = 'GET')
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request($method, 'https://graph.facebook.com/v15.0'.$endpoint, [
            'query' => ['access_token' => $token]
        ]);
        return json_decode($response->getBody(), true);
    }

    // List user's Facebook accounts
    public function accounts()
    {
        $accounts = auth()->user()->facebookAccounts;
        return view('facebook.accounts', compact('accounts'));
    }
}