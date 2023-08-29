<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobs extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'batch_id',
        'user_id'
    ];
}
