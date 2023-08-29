<?php

namespace App\Jobs;

use App\Events\VideoUpdate;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateVideo implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $videoId = '';
    public $videoTitle = '';
    public $description = '';
    public $videoOwnerChannelTitle = '';
    public $publishedAt = '';
    public $thumbnail = '';

    public $playlistId = '';
    public $subtitleUpdatedAt = 0;
    public $userId = 0;
    public $totalJobs = 0;

     /**
     * The unique ID of the job.
     */
    // public function uniqueId(): string
    // {
    //     return $this->videoId;
    // }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($snippet, $subtitleUpdatedAt, $playlistId, $userId)
    {
        $this->videoId = $snippet->resourceId->videoId;
        $this->videoTitle = $snippet->title;
        $this->description = $snippet->description;
        $this->videoOwnerChannelTitle = $snippet->videoOwnerChannelTitle;
        $this->publishedAt = $snippet->publishedAt;
        // $this->thumbnail = $snippet->thumbnails->default->url;
        $this->playlistId = $playlistId;
        $this->userId = $userId;
        $this->totalJobs = 0;

        if(isset($snippet->thumbnails->maxres)) {
            $this->thumbnail = $snippet->thumbnails->maxres->url;
        } elseif (isset($snippet->thumbnails->standard)) {
            $this->thumbnail = $snippet->thumbnails->standard->url;
        } elseif (isset($snippet->thumbnails->high)) {
            $this->thumbnail = $snippet->thumbnails->high->url;
        } elseif (isset($snippet->thumbnails->medium)) {
            $this->thumbnail = $snippet->thumbnails->medium->url;
        } else {
            $this->thumbnail = $snippet->thumbnails->default->url;
        }

        $this->subtitleUpdatedAt = date('Y-m-d H:i:s', $subtitleUpdatedAt);
        

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Playlist::updateOrCreate([
            'playlist_id' => $this->playlistId,
            'video_id' => $this->videoId,
            'user_id' => $this->userId,
        ]);

        Video::updateOrCreate([
            'video_id' => $this->videoId,
            'video_title' => $this->videoTitle,
            'description' => $this->description,
            'video_owner_channel_title' => $this->videoOwnerChannelTitle,
            'published_at' => $this->publishedAt,
            'thumbnail' => $this->thumbnail,
            'total_jobs' => 0,
            'subtitle_updated_at' => $this->subtitleUpdatedAt,
        ]);
    }
}
