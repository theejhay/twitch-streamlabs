<?php

namespace App\Repository\Interface;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param $twitchId
     * @return User|null
     */
    public function findByTwitch($twitchId):?User;

    public function store($twitchUserName, $twitchUserEmail, $twitchUserId, $twitchUserLogin, $twitchUserToken, $twitchUserRefreshToken):User;

    public function refreshToken($twitchId, $token, $refreshToken);
}
