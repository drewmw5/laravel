<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Video extends Model
{
    use HasFactory, Searchable;

    protected $primaryKey = 'video_id';
    public $incrementing = false;
    protected $keyType = 'string';

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

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description
        ];
    }

    public function captions(): HasMany 
    {
        return $this->hasMany(Caption::class);
    }

    private static function addJobBatchesToVideo(Collection $videos): Collection {
        foreach ($videos as $index => $video) {
            $jobBatches = DB::table("job_batches")->where('name', '=', $video->video_id)->get();
            $videos[$index]['job_batches'] = $jobBatches;
        }
        return $videos;
    }

    public static function getAllWithJobBatches()
    {
        $videos = Video::orderBy('published_at', 'desc')->get();
        $videosWithJobBatches = Video::addJobBatchesToVideo($videos);

        return $videosWithJobBatches;

    }
}
