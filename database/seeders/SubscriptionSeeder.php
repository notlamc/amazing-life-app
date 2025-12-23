<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::insert([
            [
                'name' => 'Basic Plan',
                'description' => 'This is the basic monthly subscription plan.',
                'price' => 9.00,
                'status' => 'active',
                'duration_days' => 30,
                'commission_percentage' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'A premium 3-month subscription with exclusive features.',
                'price' => 99.00,
                'status' => 'active',
                'duration_days' => 90,
                'commission_percentage' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Yearly Plan',
                'description' => 'Full yearly access to all premium features.',
                'price' => 999.00,
                'status' => 'active',
                'duration_days' => 365,
                'commission_percentage' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
