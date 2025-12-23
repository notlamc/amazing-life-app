<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ContinueWatching extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'last_watched_second',
        'duration',
        'progress_percent'
    ];
}
