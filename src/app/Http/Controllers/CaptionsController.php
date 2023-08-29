<?php

namespace App\Http\Controllers;

use App\Jobs\CreateVideo;
use App\Jobs\ProcessTranscript;
use App\Jobs\SyncUserJobs;
use Exception;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Response;
use App\Models\Captions;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;

class CaptionsController extends Controller
{
    private $items = [];

    function getPlaylistData(string $url)
    {
        $client = new GuzzleClient();

        $request = $client
            ->request('GET', $url)
            ->getBody();

        return $request;
    }

    function getNextPage(string $urlWithPlaylistId, string $nextPageToken)
    {

        $updatedUrl = $urlWithPlaylistId . "&pageToken={$nextPageToken}";
        $nextPage = $this->getPlaylistData($updatedUrl);

        $nextPage = json_decode($nextPage);

        foreach ($nextPage->items as $index => $item) {
            array_push($this->items, $item);
        }
        if (isset($nextPage->nextPageToken)) {
            $this->getNextPage($urlWithPlaylistId, $nextPage->nextPageToken);
        } else {
            foreach ($nextPage->items as $index => $item) {
                array_push($this->items, $item);
            }
        }
    }

    function getSubtitleDate($videoId) {
        $lastUpdatedAtUrl = "https://youtube.googleapis.com/youtube/v3/captions?key=AIzaSyDrh-ihPqtyr0TlDRMS665gbGyUbGecXOk&part=snippet&videoId={$videoId}"; // 50 quota cost per call
        $lastUpdatedAt = $this->getPlaylistData($lastUpdatedAtUrl);
        if(!isset(json_decode($lastUpdatedAt)->items[0])) return false;
        return date('Y-m-d H:i:s', strtotime(json_decode($lastUpdatedAt)->items[0]->snippet->lastUpdated));
    }

    function checkIfVideoExists($item) : false|object {
                
        // Check if the video already exists in the database
        // This is done to prevent over querying the YouTube API
        $video = Video::where('video_id', '=', $item->snippet->resourceId->videoId)
        ->get();

        // Returns the object from the db if true, false if not found
        return isset($video[0]) ? $video[0] : false;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = "https://youtube.googleapis.com/youtube/v3/playlistItems?key=AIzaSyDrh-ihPqtyr0TlDRMS665gbGyUbGecXOk&part=snippet&maxResults=50"; // 1 quota cost per call

        $data = $request->all();

        $data = $data['data'];

        $playlistId = $data['playlistId'];

        $url = $url . "&playlistId={$playlistId}";

        $response = $this->getPlaylistData($url);
        $response = json_decode($response);

        foreach ($response->items as $item) {
            $videoId = $item->snippet->resourceId->videoId;
            $this->items[$videoId] = $item;
        }

        if ($response->nextPageToken) {
            $this->getNextPage($url, $response->nextPageToken);
        }
            
        $return = [];

        foreach ($this->items as $index => $item) {
            $checkIfVideoExists = $this->checkIfVideoExists($item);
            $subtitleDate = time();

            if($checkIfVideoExists) {
                $latestDate = $this->getSubtitleDate($item->snippet->resourceId->videoId); // Makes the API call here!!!
                if ($latestDate == false)
                    continue;
                $latestDate = strtotime($subtitleDate);
                
                $subtitleUpdatedAt = strtotime($checkIfVideoExists->subtitle_updated_at);
                $updatedAt = strtotime($checkIfVideoExists->updated_at);

                if($subtitleUpdatedAt === $latestDate) 
                    continue;
                if($latestDate <= $updatedAt) 
                    continue;

            }

            $batch = Bus::batch([]);
            $batch->add([
                new CreateVideo($item->snippet, $subtitleDate, $playlistId, 1),
                new ProcessTranscript($item->snippet->resourceId->videoId),
                ])
                ->name($item->snippet->resourceId->videoId)
                ->dispatch();

            $videoId = $item->snippet->resourceId->videoId;

            $return[$videoId] = $batch;
            
        }
        return response($return);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Captions  $captions
     * @return \Illuminate\Http\Response
     */
    public function show(Captions $captions, Request $request)
    {

        $results = [];

        $data = $request->all();

        $captions = Captions::where('text', 'LIKE', '%' . $data['search'] . '%')
            ->orderBy('video_id')
            ->orderBy('start')
            ->get();
        
        foreach($captions as $index => $caption) {
            $videoId = $caption->video_id;
            $results[$videoId]['captions'][]['caption'] = $caption;
        }

        foreach($results as $videoId => $video) {
            $video = Video::where('video_id', '=', $videoId)
                ->get();
            [$results[$videoId]['video']] = $video;

            foreach($results[$videoId]['captions'] as $index => $caption) {
                $caption = $caption['caption'];
                $nextCaption = Captions::where('video_id', '=', $caption->video_id)
                    ->where('start', '>', $caption->start)
                    ->orderBy('start', 'ASC')
                    ->first();

                $prevCaption = Captions::where('video_id', '=', $caption->video_id)
                    ->where('start', '<', $caption->start)
                    ->orderBy('start', 'DESC')
                    ->first();

                $results[$videoId]['captions'][$index]['prevCaption'] = $prevCaption;
                $results[$videoId]['captions'][$index]['nextCaption'] = $nextCaption;
            }
        }

        return response($results);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Captions  $captions
     * @return \Illuminate\Http\Response
     */
    public function edit(Captions $captions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Captions  $captions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Captions $captions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Captions  $captions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Captions $captions)
    {
        //
    }
}
