<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RegionalManagerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'manager@sms.com'],
            [
                'name'      => 'General Manager',
                'password'  => Hash::make('Manager@1234'),
                'branch_id' => null, // not tied to a branch
                'is_active' => true,
            ]
        );

        $user->syncRoles(['general_manager']);

        $this->command->info('General Manager created: manager@sms.com / Manager@1234');
    }
}
