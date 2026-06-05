<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('Admin@1234'),
                'is_active' => true,
            ]
        );

        $admin->assignRole('super_admin');
    }
}
