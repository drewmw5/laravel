<?php

namespace App\Http\Controllers;

use App\Models\UserJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserJobsController extends Controller
{
    public function index() {
        $query = UserJobs::select([
            'job_batches.*',
            'videos.video_title',
            'videos.description',
            'videos.thumbnail'])
            ->from('job_batches')
            ->join('user_jobs', 'job_batches.id', '=', 'user_jobs.batch_id')
            ->join('videos', 'job_batches.name', '=', 'videos.video_id')
            ->where('user_jobs.user_id', '=', Auth::user()->id)->get();

        return $query;
    }
    
}
