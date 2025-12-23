<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Admin\Admin;

class WalletPaymentTransaction extends Model
{
    protected $table = 'wallet_payment_transactions';

    protected $fillable = [
        'user_id',
        'wallet_id',
        'type',
        'amount',
        'transaction_id',
        'description',
        'status',
        'admin_id',
        'approved_at',
        'rejected_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
