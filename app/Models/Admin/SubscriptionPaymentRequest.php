<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'status',
        'payment_reference',
        'request_payload',
        'paypal_order_id',
        'response_payload'
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
