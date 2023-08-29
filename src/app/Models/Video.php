<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'video_title',
        'description',
        'video_owner_channel_title',
        'published_at',
        'thumbnail',
        'total_jobs',
        'subtitle_updated_at'
    ];

}
