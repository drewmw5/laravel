<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ProcessTranscript implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $videoId = '';

     /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->videoId;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($videoId)
    {
        $this->videoId = $videoId;
        Log::info($videoId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $processOutput = '';

        $process = new Process(['youtube_transcript_api', '--cookies', './cookies_4.txt', '--format', 'json', "$this->videoId"]);

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
            Log::info($line);
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        $processOutput = json_decode($processOutput);


        if ($processOutput === null) {
            return;
        } else {
            foreach($processOutput as $index => $caption) {
                $this->batch()->add(
                    new HandleCaption($caption, $this->videoId)
                );
            }
        }
    }
}
