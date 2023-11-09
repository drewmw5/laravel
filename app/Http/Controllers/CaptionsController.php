<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTranscript;
use App\Jobs\SyncUserJobs;
use Exception;


use Illuminate\Http\Response;
use App\Models\Caption;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaptionsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function show(Caption $captions, Request $request)
    {

        $results = [];

        $data = $request->all();

        $captions = Caption::where('text', 'LIKE', '%' . $data['search'] . '%')
            ->orderBy('video_id')
            ->orderBy('start')
            ->get();

        foreach ($captions as $index => $caption) {
            $videoId = $caption->video_id;
            $results[$videoId]['captions'][]['caption'] = $caption;
        }

        foreach ($results as $videoId => $video) {
            $video = Video::where('video_id', '=', $videoId)
                ->get();
            [$results[$videoId]['video']] = $video;

            foreach ($results[$videoId]['captions'] as $index => $caption) {
                $caption = $caption['caption'];
                $nextCaption = Caption::where('video_id', '=', $caption->video_id)
                    ->where('start', '>', $caption->start)
                    ->orderBy('start', 'ASC')
                    ->first();

                $prevCaption = Caption::where('video_id', '=', $caption->video_id)
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
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function edit(Caption $captions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caption $captions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Caption  $captions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caption $captions)
    {
        //
    }
}
