<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\ContinueWatching;

class ContinueWatchingSeeder extends Seeder
{
    public function run()
    {
        ContinueWatching::create([
            'user_id' => 1,
            'video_id' => 1,
            'last_watched_second' => 120,
            'duration' => 287,
            'progress_percent' => 48
        ]);
    }
}
