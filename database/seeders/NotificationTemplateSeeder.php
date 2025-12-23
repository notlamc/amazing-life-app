<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        NotificationTemplate::create([
            'name' => 'Welcome Email',
            'type' => 'email',
            'subject' => 'Welcome to Our App',
            'body' => '<h1>Hello {{name}}</h1><p>Thanks for joining us.</p>',
            'variables' => json_encode(['name']),
        ]);

        NotificationTemplate::create([
            'name' => 'Login Alert Push',
            'type' => 'push',
            'subject' => null,
            'body' => 'Hi {{name}}, a new login was detected.',
            'variables' => json_encode(['name']),
        ]);
    }
}
