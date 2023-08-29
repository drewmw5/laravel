<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show(Request $request) {
        return $request;
        Video::select()
            ->where('video_id', '=', );
    }
}
