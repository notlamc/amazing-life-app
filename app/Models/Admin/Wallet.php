<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // ✅ Helper: credit wallet
    public function credit(float $amount, string $description = null)
    {
        $this->increment('balance', $amount);
        $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    // ✅ Helper: debit wallet
    public function debit(float $amount, string $description = null)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
            ]);
        }
    }
}
