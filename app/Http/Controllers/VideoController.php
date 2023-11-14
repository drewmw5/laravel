<?php

namespace App\Http\Controllers;

use App\Models\Caption;
use GuzzleHttp\Client as GuzzleClient;
use App\Jobs\ProcessTranscript;
use App\Jobs\TimelineToTags;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class VideoController extends Controller
{

    private $videoURL = "https://youtube.googleapis.com/youtube/v3/videos?part=snippet&key=AIzaSyDrh-ihPqtyr0TlDRMS665gbGyUbGecXOk";

    private $playlistURL = "https://youtube.googleapis.com/youtube/v3/playlistItems?key=AIzaSyDrh-ihPqtyr0TlDRMS665gbGyUbGecXOk&part=snippet&maxResults=50&playlistId=PLr_9yu0xXGOdxBfDsXVVsmRW6S5G0xMBN"; // 1 quota cost per call


    private $items = [];



    private function get(string $url): object
    {
        $client = new GuzzleClient();

        $request = $client
            ->request('GET', $url)
            ->getBody();

        return json_decode($request);
    }

    private function getNextPage(string $nextPageToken)
    {

        $updatedUrl = $this->playlistURL . "&pageToken={$nextPageToken}";
        $nextPage = $this->get($updatedUrl);


        foreach ($nextPage->items as $index => $item) {
            array_push($this->items, $item);
        }
        if (isset($nextPage->nextPageToken)) {
            $this->getNextPage($nextPage->nextPageToken);
        } else {
            foreach ($nextPage->items as $index => $item) {
                array_push($this->items, $item);
            }
        }
    }

    private function getHighestResThumbnail($thumbnails): string {
        if (isset($thumbnails->maxres)) {
            $thumbnail = $thumbnails->maxres->url;
        } elseif (isset($thumbnails->standard)) {
            $thumbnail = $thumbnails->standard->url;
        } elseif (isset($thumbnails->high)) {
            $thumbnail = $thumbnails->high->url;
        } elseif (isset($thumbnails->medium)) {
            $thumbnail = $thumbnails->medium->url;
        } else {
            $thumbnail = $thumbnails->default->url;
        }

        return $thumbnail;
    }

    private function videoUpdateOrCreate($videoId, $videoTitle, $description, $owner, $publishedAt, $thumbnail) {
        Video::updateOrCreate([
            'video_id' => $videoId,
        ], [
            'video_title' => $videoTitle,
            'description' => $description,
            'video_owner_channel_title' => $owner,
            'published_at' => $publishedAt,
            'thumbnail' => $thumbnail,
            // 'subtitle_updated_at' => $item->snippet->subtitleUpdatedAt,
        ]);
    }

    private function startWork($item)
    {
        $this->videoUpdateOrCreate(
            $item->snippet->resourceId->videoId,
            $item->snippet->title,
            $item->snippet->description,
            $item->snippet->videoOwnerChannelTitle,
            $item->snippet->publishedAt,
            $this->getHighestResThumbnail($item->snippet->thumbnails));


        $batch = Bus::batch([]);
        $batch->add([
            new ProcessTranscript($item->snippet->resourceId->videoId),
        ])
            ->name($item->snippet->resourceId->videoId)
            ->dispatch();
    }

            /**
     * Store a single newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeSingle(Request $request)
    {
        $data = $request->all();
        $videoId = $data['videoId'];

        $url = $this->videoURL . "&id=" . $data['videoId'];
        $getVideoData = $this->get($url);

        $video = $getVideoData->items[0];

        $this->videoUpdateOrCreate(
            $videoId,
            $video->snippet->title,
            $video->snippet->description,
            $video->snippet->channelTitle,
            $video->snippet->publishedAt,
            $this->getHighestResThumbnail($video->snippet->thumbnails)
        );

        TimelineToTags::dispatch($videoId, $video->snippet->description);

        $batch = Bus::batch([]);
        $batch->add([
            new ProcessTranscript("$videoId"),
        ])
            ->name($data['videoId'])
            ->dispatch();

        return response(json_encode($batch));
    }

        /**
     * Store a newly created resource array in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeMany(Request $request): void
    {
        $response = $this->get($this->playlistURL);

        foreach ($response->items as $item) {
            $this->items[] = $item;
        }

        if (isset($response->nextPageToken)) {
            $this->getNextPage($response->nextPageToken);
        }

        foreach ($this->items as $index => $item) {

            $video = Video::where('video_id', '=', $item->snippet->resourceId->videoId)
                ->first('video_id');

            if ($video) {
                $caption = Caption::where('video_id', '=', $video->video_id)->first();
                if (!$caption) {
                    $this->startWork($item);
                }
            } else {
                $this->startWork($item);
            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $captions
     * @return \Illuminate\Http\Response
     */
    public function show(Video $videos, Request $request)
    {
        return response(Video::getAllWithJobBatches());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $captions
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $videos)
    {
        return response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Video  $captions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $videos)
    {
        return response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $captions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $videos)
    {
        return response();
    }
}
