<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\WalletTransaction;
use App\Models\Admin\Wallet;

class WalletTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $wallets = Wallet::all();

        foreach ($wallets as $wallet) {
            for ($i = 1; $i <= 3; $i++) {
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => rand(0, 1) ? 'credit' : 'debit',
                    'subscription_price' => rand(50, 500),
                    'balance_amount' => rand(50, 500),
                    'description' => 'Test transaction ' . $i,
                    'user_id' =>  $i,
                    'purchased_id' =>  $i,
                    'commission_percentage' => 10,
                    'commission_amount' => 10,
                    'subscription_id' => 1,
                    'status' => 'approved',
                ]);
            }
        }
    }
}
