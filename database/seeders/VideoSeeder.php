<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Video;
use App\Models\Admin\User;
use Illuminate\Support\Str;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                Video::create([
                    'title' => 'Sample Video ' . $i . ' by ' . $user->name,
                    'description' => 'This is a sample description for video ' . $i . '.',
                    'video_path' => 'uploads/videos/video_' . $i . '_' . Str::random(6) . '.mp4',
                    'thumbnail_path' => 'uploads/thumbnails/thumb_' . $i . '.jpg',
                    'duration' => rand(60, 600),
                    'status' => 'active',
                    'views' => rand(10, 1000),
                    'likes' => rand(1, 500),
                ]);
            }
        }
    }
}
