<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Branch management
            'view branches', 'create branches', 'edit branches', 'delete branches',

            // User management
            'view users', 'create users', 'edit users', 'delete users',

            // Product management
            'view products', 'create products', 'edit products', 'delete products',

            // Inventory
            'view stock', 'adjust stock', 'view stock movements',

            // Sales
            'view sales', 'create sales', 'cancel sales', 'view all branch sales',

            // Transfers
            'view transfers', 'create transfers', 'approve transfers',
            'dispatch transfers', 'receive transfers',

            // Returns
            'view returns', 'create returns', 'approve returns',

            // Financial years
            'view financial years', 'manage financial years',

            // Banking
            'view deposits', 'create deposits', 'verify deposits',

            // Reports
            'view reports', 'export reports',

            // Audit
            'view audit logs',

            // Dashboard
            'view dashboard',

            // Supplier
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view purchase orders', 'create purchase orders', 'edit purchase orders',
            'approve purchase orders', 'receive purchase orders',
            'view supplier payments', 'create supplier payments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- SUPER ADMIN ---
        // Gets all permissions automatically via gate override (see below)
        Role::firstOrCreate(['name' => 'super_admin']);

        // --- BRANCH ADMINISTRATOR ---
        $branchAdmin = Role::firstOrCreate(['name' => 'branch_admin']);
        $branchAdmin->syncPermissions([
            'view branches',
            'view users', 'create users', 'edit users',
            'view products', 'create products', 'edit products',
            'view stock', 'adjust stock', 'view stock movements',
            'view sales', 'create sales', 'cancel sales',
            'view transfers', 'create transfers', 'approve transfers',
            'dispatch transfers', 'receive transfers',
            'view returns', 'create returns', 'approve returns',
            'view financial years',
            'view deposits', 'create deposits',
            'view reports', 'export reports',
            'view dashboard',
        ]);

        // --- STORE MANAGER ---
        $storeManager = Role::firstOrCreate(['name' => 'store_manager']);
        $storeManager->syncPermissions([
            'view products', 'create products', 'edit products',
            'view stock', 'adjust stock', 'view stock movements',
            'view sales', 'create sales', 'cancel sales',
            'view transfers', 'create transfers', 'approve transfers',
            'dispatch transfers', 'receive transfers',
            'view returns', 'create returns', 'approve returns',
            'view deposits', 'create deposits',
            'view reports', 'export reports',
            'view dashboard', 'view suppliers', 'create suppliers', 'edit suppliers',
            'view purchase orders', 'create purchase orders', 'edit purchase orders',
            'receive purchase orders',
            'view supplier payments', 'create supplier payments',
        ]);

        // --- SALES OFFICER / CASHIER ---
        $salesOfficer = Role::firstOrCreate(['name' => 'sales_officer']);
        $salesOfficer->syncPermissions([
            'view products',
            'view stock',
            'view sales', 'create sales',
            'view returns', 'create returns',
            'view dashboard',
        ]);

        // --- STOCK OFFICER ---
        $stockOfficer = Role::firstOrCreate(['name' => 'stock_officer']);
        $stockOfficer->syncPermissions([
            'view products', 'create products', 'edit products',
            'view stock', 'adjust stock', 'view stock movements',
            'view transfers', 'create transfers', 'receive transfers',
            'view dashboard',
        ]);

        // --- AUDITOR ---
        $auditor = Role::firstOrCreate(['name' => 'auditor']);
        $auditor->syncPermissions([
            'view branches',
            'view products',
            'view stock', 'view stock movements',
            'view sales', 'view all branch sales',
            'view transfers',
            'view returns',
            'view deposits', 'verify deposits',
            'view reports', 'export reports',
            'view audit logs',
            'view dashboard',
        ]);

        // --- ACCOUNTANT ---
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'view sales', 'view all branch sales',
            'view deposits', 'verify deposits',
            'view financial years', 'manage financial years',
            'view reports', 'export reports',
            'view dashboard', 'view suppliers',
            'view purchase orders', 'approve purchase orders',
            'view supplier payments', 'create supplier payments',
        ]);
    }
}
