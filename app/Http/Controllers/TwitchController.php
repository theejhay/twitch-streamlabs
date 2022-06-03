<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\Interface\TwitchRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class TwitchController extends Controller
{
    private TwitchRepositoryInterface $twitchRepository;

    /**
     * @param TwitchRepositoryInterface $twitchRepository
     */
    public function __construct(TwitchRepositoryInterface $twitchRepository)
    {
        $this->twitchRepository = $twitchRepository;

        //Fetch Twitch token, refresh token from a DB instead of Auth::user()

        $user = User::where('email', 'ogunyemitaiwojohn@gmail.com')->first();
        $this->twitchRepository->init([
            'twitch_url' => env('TWITCH_URL'),
            'twitch_token_refresh_url' => env('TWITCH_TOKEN_REFRESH_URL'),
            'token'=>$user->twitch_token,
            'refresh_token'=>$user->twitch_refresh_token,
            'client_id'=>env('TWITCH_CLIENT_ID'),
            'client_secret'=>env('TWITCH_CLIENT_SECRET'),
            'twitch_id'=>$user->twitch_id
        ]);
    }

    public function fetchListOfTags()
    {
        $this->twitchRepository->fetchListOfTags();
    }

    public function fetchTopStreams()
    {
        $this->twitchRepository->fetchTopStreams();
    }

    public function fetchUserStreams()
    {
        $this->twitchRepository->init([
            'twitch_url' => env('TWITCH_URL'),
            'twitch_token_refresh_url' => env('TWITCH_TOKEN_REFRESH_URL'),
            'token'=>Auth::user()->twitch_token,
            'refresh_token'=>Auth::user()->twitch_refresh_token,
            'client_id'=>env('TWITCH_CLIENT_ID'),
            'client_secret'=>env('TWITCH_CLIENT_SECRET'),
            'twitch_id'=>Auth::user()->twitch_id
        ]);
        $this->twitchRepository->fetchUserStreams();

        return redirect(route('twitch-dashboard'));
    }

}
