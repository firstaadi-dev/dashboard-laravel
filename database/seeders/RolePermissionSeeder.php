<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each module
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',

            // Customer Management (CRM)
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',

            // Supplier Management
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Product Management
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Transaction/Sales
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
            'approve_transactions',

            // Purchase Orders
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'approve_purchase_orders',

            // Stock/Inventory
            'view_stock_movements',
            'create_stock_movements',
            'edit_stock_movements',
            'delete_stock_movements',
            'adjust_stock',

            // Employee Management (HR)
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',

            // Attendance
            'view_attendances',
            'create_attendances',
            'edit_attendances',
            'delete_attendances',

            // Accounting
            'view_accounts',
            'create_accounts',
            'edit_accounts',
            'delete_accounts',

            // Journal Entries
            'view_journal_entries',
            'create_journal_entries',
            'edit_journal_entries',
            'delete_journal_entries',
            'post_journal_entries',

            // Reports
            'view_sales_reports',
            'view_inventory_reports',
            'view_financial_reports',
            'view_hr_reports',
            'export_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // 1. Super Admin - Full Access
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Almost full access (no role management)
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            // Users (view only)
            'view_users',

            // Full CRM
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers',

            // Full Suppliers
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers',

            // Full Products
            'view_products', 'create_products', 'edit_products', 'delete_products',

            // Full Transactions
            'view_transactions', 'create_transactions', 'edit_transactions', 'delete_transactions', 'approve_transactions',

            // Full Purchase Orders
            'view_purchase_orders', 'create_purchase_orders', 'edit_purchase_orders', 'delete_purchase_orders', 'approve_purchase_orders',

            // Full Stock
            'view_stock_movements', 'create_stock_movements', 'edit_stock_movements', 'delete_stock_movements', 'adjust_stock',

            // Full HR
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'view_attendances', 'create_attendances', 'edit_attendances', 'delete_attendances',

            // Full Accounting
            'view_accounts', 'create_accounts', 'edit_accounts', 'delete_accounts',
            'view_journal_entries', 'create_journal_entries', 'edit_journal_entries', 'delete_journal_entries', 'post_journal_entries',

            // All Reports
            'view_sales_reports', 'view_inventory_reports', 'view_financial_reports', 'view_hr_reports', 'export_reports',
        ]);

        // 3. Manager - View and approve, limited edit
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            // View users
            'view_users',

            // CRM - View and Create
            'view_customers', 'create_customers', 'edit_customers',

            // Suppliers - View only
            'view_suppliers',

            // Products - View and Edit
            'view_products', 'edit_products',

            // Transactions - View and Approve
            'view_transactions', 'approve_transactions',

            // Purchase Orders - View and Approve
            'view_purchase_orders', 'approve_purchase_orders',

            // Stock - View and Adjust
            'view_stock_movements', 'adjust_stock',

            // HR - View and manage attendance
            'view_employees',
            'view_attendances', 'create_attendances', 'edit_attendances',

            // Accounting - View only
            'view_accounts',
            'view_journal_entries',

            // All Reports
            'view_sales_reports', 'view_inventory_reports', 'view_financial_reports', 'view_hr_reports', 'export_reports',
        ]);

        // 4. Cashier - Sales and basic inventory
        $cashier = Role::create(['name' => 'cashier']);
        $cashier->givePermissionTo([
            // Customers - View and Create
            'view_customers', 'create_customers',

            // Products - View only
            'view_products',

            // Transactions - Full access
            'view_transactions', 'create_transactions', 'edit_transactions',

            // Stock - View only
            'view_stock_movements',

            // Reports - Sales only
            'view_sales_reports',
        ]);

        // 5. Staff - Limited access
        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo([
            // Customers - View only
            'view_customers',

            // Products - View only
            'view_products',

            // Transactions - View only
            'view_transactions',

            // Stock - View only
            'view_stock_movements',

            // Attendance - View own attendance
            'view_attendances',
        ]);

        // Create a default super admin user if doesn't exist
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Default Super Admin: admin@example.com / password');
    }
}
