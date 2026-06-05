<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Customer;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $electronics = ProductCategory::firstOrCreate(
            ['name' => 'Electronics'],
            ['description' => 'Electronic devices', 'is_active' => true]
        );

        $accessories = ProductCategory::firstOrCreate(
            ['name' => 'Accessories'],
            ['description' => 'Product accessories', 'is_active' => true]
        );

        // Products
        $products = [
            [
                'name'          => 'Samsung Galaxy A54',
                'sku'           => 'ELEC-001',
                'category_id'   => $electronics->id,
                'unit'          => 'piece',
                'cost_price'    => 700,
                'selling_price' => 850,
                'reorder_level' => 5,
                'is_active'     => true,
            ],
            [
                'name'          => 'iPhone 14',
                'sku'           => 'ELEC-002',
                'category_id'   => $electronics->id,
                'unit'          => 'piece',
                'cost_price'    => 2800,
                'selling_price' => 3200,
                'reorder_level' => 3,
                'is_active'     => true,
            ],
            [
                'name'          => 'USB-C Cable',
                'sku'           => 'ACC-001',
                'category_id'   => $accessories->id,
                'unit'          => 'piece',
                'cost_price'    => 15,
                'selling_price' => 25,
                'reorder_level' => 20,
                'is_active'     => true,
            ],
            [
                'name'          => 'Phone Case',
                'sku'           => 'ACC-002',
                'category_id'   => $accessories->id,
                'unit'          => 'piece',
                'cost_price'    => 20,
                'selling_price' => 35,
                'reorder_level' => 15,
                'is_active'     => true,
            ],
            [
                'name'          => 'Wireless Earbuds',
                'sku'           => 'ELEC-003',
                'category_id'   => $electronics->id,
                'unit'          => 'piece',
                'cost_price'    => 120,
                'selling_price' => 180,
                'reorder_level' => 10,
                'is_active'     => true,
            ],
        ];

        $branches = Branch::all();

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );

            // Add stock to each branch
            foreach ($branches as $branch) {
                BranchStock::firstOrCreate(
                    ['branch_id' => $branch->id, 'product_id' => $product->id],
                    ['quantity' => rand(10, 100), 'last_updated' => now()]
                );
            }
        }

        // Demo customers
        $customers = [
            ['name' => 'John Mensah',   'phone' => '024-000-0001', 'email' => 'john@example.com'],
            ['name' => 'Ama Owusu',     'phone' => '054-000-0002', 'email' => 'ama@example.com'],
            ['name' => 'Kofi Asante',   'phone' => '020-000-0003', 'email' => 'kofi@example.com'],
            ['name' => 'Abena Darko',   'phone' => '059-000-0004', 'email' => 'abena@example.com'],
            ['name' => 'Kwame Boateng', 'phone' => '026-000-0005', 'email' => 'kwame@example.com'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }

        $this->command->info('Demo data seeded successfully.');
    }
}
