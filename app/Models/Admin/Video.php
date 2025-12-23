<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'categories',
        'video_path',
        'thumbnail_path',
        'duration',
        'status',
        'views',
        'likes',
        'tags',
        'metatags'
    ];

    protected $casts = [
        'duration' => 'integer',
        'views' => 'integer',
        'likes' => 'integer',
    ];

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'video_category', 'video_id', 'category_id')
                    ->withTimestamps();
    }

    public function videoCategories()
    {
        return $this->belongsToMany(Category::class, 'video_category', 'video_id', 'category_id');
    }

    public function offlineDownloads()
    {
        return $this->hasMany(OfflineDownload::class, 'video_id');
    }

}
