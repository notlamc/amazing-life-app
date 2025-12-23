<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SubscriptionSeeder::class,
            WalletSeeder::class,
            SubscriptionPaymentRequestSeeder::class,
            UserSubscriptionSeeder::class,
            WalletTransactionSeeder::class,
            VideoSeeder::class,
            AdminSeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }

}
