<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\FinancialYear;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(['code' => 'HQ'], [
            'name'      => 'Head Office',
            'address'   => 'Accra, Ghana',
            'phone'     => '030-000-0001',
            'is_active' => true,
        ]);

        Branch::firstOrCreate(['code' => 'BR01'], [
            'name'      => 'Branch One',
            'address'   => 'Kumasi, Ghana',
            'phone'     => '032-000-0001',
            'is_active' => true,
        ]);

        // Create the current financial year
        FinancialYear::firstOrCreate(['name' => 'FY 2025/2026'], [
            'start_date' => '2025-01-01',
            'end_date'   => '2025-12-31',
            'is_active'  => true,
            'is_closed'  => false,
        ]);
    }
}
