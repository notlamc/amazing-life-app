<?php

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'gender',
        'phone_number',
        'age',
        'password',
        'pass',
        'otp',
        'profile',
        'google_id',
        'facebook_id',
        'apple_id',
        'avatar',
        'status',
        'created_at',
        'updated_at',
        'referred_by',
        'referral_code',
        'used_referral_code',
        'authenticationProvider',
        'authenticationSocialId'
    ]; 
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class,'user_id','id');
    }

    public function subscriptionRequests()
    {
        return $this->hasMany(SubscriptionPaymentRequest::class);
    }

    public function referralUsers()
    {
        return $this->hasMany(User::class, 'used_referral_code', 'referral_code');
    }
}
