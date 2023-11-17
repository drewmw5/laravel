<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class Caption extends Model
{
    use HasFactory, Searchable;

    protected $primaryKey = 'id';
    
    public $timestamps = false;

    protected $fillable = [
        'video_id',
        'text',
        'start',
        'duration',
    ];

    public function toSearchableArray()
    {
        return [
            'text' => $this->text,
        ];
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function next() 
    {
        return static::where('video_id', $this->video_id) 
            ->where('start', '>', 1)
            ->orderBy('start', 'ASC')
            ->first();
    }

    public function previous()
    {
        return static::where('video_id', $this->video_id)
            ->where('start', '<', $this->start)
            ->orderBy('start', 'DESC')
            ->first();
    }

    // public static function search($query)
    // {
    //     $captions = Caption::where('text', 'LIKE', "%$query%")
    //         ->orderBy('video_id')
    //         ->orderBy('start')
    //         ->select('start', 'duration', 'text', 'video_id')
    //         ->get()
    //         ->groupBy('video_id')
    //         ->forPage(1, 5);

    //     return $captions;
    // }
}
