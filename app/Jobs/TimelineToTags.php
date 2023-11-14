<?php

namespace App\Jobs;

use App\Models\Timestamp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TimelineToTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $pattern = '/((?:\d{1,2}):?[0-5]\d:[0-5]\d):?(.*)/';

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $videoId,
        public string $description,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        preg_match_all($this->pattern, $this->description, $matches);

        foreach($matches[0] as $index => $match) {
            $exploded = explode(' -', $match);
            Log::info($exploded);

            $timestampArray[$index]['time'] = $exploded[0];
            $timestampArray[$index]['text'] = $exploded[1];
            $timestampArray[$index]['video_id'] = $this->videoId;
        }

        foreach($timestampArray as $tag) {
            Timestamp::create($tag);
        }

        // foreach($timestampArray as $tag) {

        // }
    }
}
