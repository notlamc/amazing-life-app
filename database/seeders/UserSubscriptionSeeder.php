<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\UserSubscription;
use App\Models\Admin\User;
use App\Models\Admin\Subscription;
use Carbon\Carbon;

class UserSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = Subscription::all();

        foreach (User::all() as $user) {
            $plan = $subscriptions->random();

            $start = Carbon::now()->subDays(rand(1, 15));
            $end = (clone $start)->addDays($plan->duration_days);

            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'payment_id' => $plan->id,
                'transaction_id' => 'Trans78636474847',
                'start_date' => $start,
                'end_date' => $end,
                'amount'=> 999,
                'is_active' => $end->isFuture(),
            ]);
        }
    }
}
