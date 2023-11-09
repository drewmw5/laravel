<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caption extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'video_id',
        'text',
        'start',
        'duration',
    ];
}
