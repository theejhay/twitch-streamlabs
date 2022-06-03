<?php

namespace App\Repository\Interface;

interface TwitchRepositoryInterface
{
    public function init($params);
    public function fetchListOfTags();
    public function fetchTopStreams();
    public function fetchUserStreams();
}
