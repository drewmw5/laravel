<?php

namespace App\Jobs;
use App\Events\CaptionUpdate;
use App\Jobs\Captions\CreateCaption;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class HandleCaption implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $captions = [];
    public $videoId = '';
    // public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($captions, $videoId)
    {
        $this->captions = $captions;
        $this->videoId = $videoId;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $captions = $this->captions;
        $videoId = $this->videoId;

        foreach ($captions as $index => $caption) {
            $this->batch()->add([
                new CreateCaption($caption, $videoId),
            ]);
            CaptionUpdate::dispatch($caption->text, $videoId, $index);
        }
    }
}
