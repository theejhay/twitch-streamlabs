<?php

namespace App\Http\Controllers;

use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect(route('login'));
    }

    public function loginWithTwitch()
    {
        return Socialite::driver('twitch')
            ->scopes(['user:read:follows', 'analytics:read:games'])
            ->redirect();
    }

    public function twitchCallback()
    {
        $twitchUser = Socialite::driver('twitch')->user();
        if (is_null($twitchUser)) {
            return redirect(404);
        }

        $twitchUserId = $twitchUser->getId();
        $twitchUserEmail = $twitchUser->getEmail();
        $twitchUserName = $twitchUser->getName();
        $twitchUserLogin = $twitchUser->user['login'];
        $twitchUserToken = $twitchUser->token;
        $twitchRefreshToken = $twitchUser->refreshToken;

        $user = $this->userRepository->findByTwitch($twitchUser->getId());
        if ($user) {
            $this->userRepository->refreshToken($twitchUserId, $twitchUserToken, $twitchRefreshToken);
        } else {
            $user = $this->userRepository
                ->store(
                    $twitchUserName,
                    $twitchUserEmail,
                    $twitchUserId,
                    $twitchUserLogin,
                    $twitchUserToken,
                    $twitchRefreshToken
                );
        }
        Auth::login($user);

        return redirect(route('fetch-user-streams'));
    }
}
