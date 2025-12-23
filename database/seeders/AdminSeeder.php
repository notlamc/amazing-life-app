<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name'      => 'Super Admin',
            'email'     => 'hemchandra@voicehub.com',
            'password'  => Hash::make('hemchandra@voicehub'), // secure hashing
            'pass'      => 'hemchandra@voicehub',             // plain password if required by your system
            'phone'     => '9999999999',
            'role'      => 'superadmin',
            'notes'     => 'Default system administrator',
            'status'    => 'active',
        ]);
    }
}
