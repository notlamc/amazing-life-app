<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;
    protected $table = 'site_settings';

    protected $fillable = [
        'site_title',
        'logo',
        'small_logo',
        'favicon',
        'referral_commission',
        'extend_duration_days',
    ];
}
