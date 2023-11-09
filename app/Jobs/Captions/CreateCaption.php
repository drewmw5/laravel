<?php

namespace App\Jobs\Captions;

use App\Models\Caption;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCaption implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $caption = [];
    public $videoId = '';
    // public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($caption, $videoId)
    {
        $this->caption = $caption;
        $this->videoId = $videoId;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $caption = $this->caption;
        $videoId = $this->videoId;

        Caption::updateOrCreate([
            'video_id' => $videoId,
            'text' => $caption->text,
            'start' => round($caption->start)
        ], [
            'video_id' => $videoId,
            'text' => $caption->text,
            'start' => round($caption->start),
            'duration' => round($caption->duration),
        ]);
    }
}