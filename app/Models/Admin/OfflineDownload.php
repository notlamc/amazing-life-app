<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class OfflineDownload extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'local_file_path',
        'downloaded_at'
    ];
}
