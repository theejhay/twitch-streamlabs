<?php

namespace App\Repository\Eloquent;

use App\Models\Stream;
use App\Models\UserStream;
use App\Repository\Interface\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getTotalNumberOfStreams()
    {
        return Stream::selectRaw('game_name, min(thumbnail_url) as thumbnail_url, count(game_name) as total')
                    ->where('game_name', '<>', null)
                    ->orderBy('total', 'desc')
                    ->groupBy('game_name')
                    ->paginate(env('ROWS_PER_PAGE'));
    }

    public function getTopGames()
    {
        return Stream::selectRaw('game_name, min(thumbnail_url) as thumbnail_url, sum(viewer_count) viewer_count')
                    ->where('game_name', '<>', null)
                    ->orderBy('viewer_count', 'desc')
                    ->groupBy('game_name')
                    ->paginate(env('ROWS_PER_PAGE'));
    }

    public function getTop100Streams($orderBy)
    {
        return Stream::selectRaw('title, thumbnail_url, viewer_count')
                            ->where('title', '<>', null)
                            ->orderBy('viewer_count', $orderBy)
                            ->take(100)
                            ->get();
    }

    public function getStreamsByHour()
    {
        return Stream::selectRaw("date_format(started_at, '%Y-%m-%d %H') datetime, count(*) as total")
                        ->groupBy("datetime")
                        ->orderBy('datetime', 'desc')
                        ->paginate(env('ROWS_PER_PAGE'));
    }

    public function getUserStreams()
    {
        $topStreams = Stream::selectRaw('id, title, thumbnail_url, viewer_count')
                    ->take(1000)
                    ->where('title', '<>', null)
                    ->orderBy('viewer_count', 'desc')
                    ->get();
        $userStreams = UserStream::where('user_id', Auth::user()->id)->pluck('stream_id')->toArray();
        $selectedStreams = array();
        foreach($topStreams as $topStream){
            if (in_array($topStream->id, $userStreams, true)){
                $selectedStreams[] = $topStream;
            }
        }
        return $selectedStreams;
    }

    public function getMedianForAllStreams()
    {
        $streamViewers =  Stream::select('viewer_count')->pluck('viewer_count')->toArray();
        return $this->calculcateMedian($streamViewers);
    }

    private function calculcateMedian($data): float|int
    {
        sort($data);
        $dataSize = count($data);
        return ($dataSize % 2 === 0) ? ($data[$dataSize / 2 - 1] + $data[$dataSize / 2]) / 2 : $data[$dataSize / 2];
    }

    public function getNumViewersToMakeTop()
    {
        $UserMinViewerCountStream = Stream::selectRaw('min(viewer_count) viewer_count, min(id) stream_id')
                                            ->whereHas('userStreams', function($query){
                                                $query->where('user_id', Auth::user()->id);
                                            })->first();
        $topStreamViewerCounts = Stream::selectRaw('viewer_count')
                            ->take(1000)
                            ->where('title', '<>', null)
                            ->where('id', '<>', $UserMinViewerCountStream->stream_id)
                            ->pluck('viewer_count')
                            ->toArray();
        $topMinViewerCountStream = min($topStreamViewerCounts);
        return $topMinViewerCountStream-$UserMinViewerCountStream->viewer_count;
    }

    // used application layer #4
    // note: Even if it is much easier and efficient to solve this task using database queries I did it using arrays because of the task requirements
    public function getSharedTags(){
        $userStreams = Stream::whereHas('userStreams', function($query){
                                $query->where('user_id', Auth::user()->id);
                            })
                            ->with('tags')
                            ->get();
        $topStreams = Stream::take(1000)->orderBy('viewer_count', 'desc')->with('tags')->get();
        $sharedTagList = array();
        foreach($topStreams as $topStream){
            foreach($userStreams as $userStream){
                $this->findSharedTags($userStream->tags, $topStream->tags, $sharedTagList);
            }
        }

        return $sharedTagList;
    }

    private function findSharedTags($userStreamTags, $topStreamTags, &$sharedTagList)
    {
        foreach($topStreamTags as $topStreamTag){
            foreach($userStreamTags as $userStreamTag){
                if ($userStreamTag->tag_id === $topStreamTag->tag_id){
                    $isNewTag = true;
                    foreach($sharedTagList as $sharedTag){
                        if ($sharedTag->tag_id === $userStreamTag->tag_id){
                            $isNewTag = false;
                            break;
                        }
                    }
                    if ($isNewTag) {
                        $sharedTagList[] = $userStreamTag;
                    }
                }
            }
        }
    }
}
