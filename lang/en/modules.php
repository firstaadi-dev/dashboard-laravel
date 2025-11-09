<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ERP Modules Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for specific ERP module
    | translations including field labels, messages, and descriptions.
    |
    */

    // Customer Module
    'customers' => [
        'title' => 'Customers',
        'singular' => 'Customer',
        'description' => 'Manage customer information and relationships',
        'fields' => [
            'customer_code' => 'Customer Code',
            'company_name' => 'Company Name',
            'contact_person' => 'Contact Person',
            'tax_number' => 'Tax Number',
            'credit_limit' => 'Credit Limit',
            'payment_terms' => 'Payment Terms',
            'website' => 'Website',
        ],
        'messages' => [
            'create_success' => 'Customer created successfully.',
            'update_success' => 'Customer updated successfully.',
            'delete_success' => 'Customer deleted successfully.',
        ],
    ],

    // Supplier Module
    'suppliers' => [
        'title' => 'Suppliers',
        'singular' => 'Supplier',
        'description' => 'Manage supplier information and relationships',
        'fields' => [
            'supplier_code' => 'Supplier Code',
            'company_name' => 'Company Name',
            'contact_person' => 'Contact Person',
            'tax_number' => 'Tax Number',
            'bank_account' => 'Bank Account',
            'bank_name' => 'Bank Name',
            'payment_terms' => 'Payment Terms',
            'website' => 'Website',
        ],
        'messages' => [
            'create_success' => 'Supplier created successfully.',
            'update_success' => 'Supplier updated successfully.',
            'delete_success' => 'Supplier deleted successfully.',
        ],
    ],

    // Product Module
    'products' => [
        'title' => 'Products',
        'singular' => 'Product',
        'description' => 'Manage product inventory and information',
        'fields' => [
            'product_code' => 'Product Code',
            'product_name' => 'Product Name',
            'sku' => 'SKU',
            'barcode' => 'Barcode',
            'category' => 'Category',
            'unit' => 'Unit',
            'purchase_price' => 'Purchase Price',
            'selling_price' => 'Selling Price',
            'stock' => 'Stock',
            'min_stock' => 'Minimum Stock',
            'max_stock' => 'Maximum Stock',
            'reorder_level' => 'Reorder Level',
            'weight' => 'Weight',
            'dimensions' => 'Dimensions',
            'is_active' => 'Active',
        ],
        'messages' => [
            'create_success' => 'Product created successfully.',
            'update_success' => 'Product updated successfully.',
            'delete_success' => 'Product deleted successfully.',
            'low_stock_warning' => 'Product stock is running low.',
            'out_of_stock' => 'Product is out of stock.',
        ],
    ],

    // Category Module
    'categories' => [
        'title' => 'Categories',
        'singular' => 'Category',
        'description' => 'Manage product categories',
        'fields' => [
            'category_name' => 'Category Name',
            'parent_category' => 'Parent Category',
            'sort_order' => 'Sort Order',
            'is_active' => 'Active',
        ],
        'messages' => [
            'create_success' => 'Category created successfully.',
            'update_success' => 'Category updated successfully.',
            'delete_success' => 'Category deleted successfully.',
        ],
    ],

    // Transaction Module
    'transactions' => [
        'title' => 'Transactions',
        'singular' => 'Transaction',
        'description' => 'Manage sales transactions',
        'fields' => [
            'transaction_number' => 'Transaction Number',
            'transaction_date' => 'Transaction Date',
            'customer' => 'Customer',
            'items' => 'Items',
            'subtotal' => 'Subtotal',
            'tax' => 'Tax',
            'discount' => 'Discount',
            'grand_total' => 'Grand Total',
            'payment_method' => 'Payment Method',
            'payment_status' => 'Payment Status',
            'paid_amount' => 'Paid Amount',
            'change' => 'Change',
            'notes' => 'Notes',
        ],
        'messages' => [
            'create_success' => 'Transaction created successfully.',
            'update_success' => 'Transaction updated successfully.',
            'delete_success' => 'Transaction deleted successfully.',
            'payment_completed' => 'Payment completed successfully.',
        ],
    ],

    // Purchase Order Module
    'purchase_orders' => [
        'title' => 'Purchase Orders',
        'singular' => 'Purchase Order',
        'description' => 'Manage purchase orders',
        'fields' => [
            'po_number' => 'PO Number',
            'po_date' => 'PO Date',
            'supplier' => 'Supplier',
            'expected_delivery_date' => 'Expected Delivery Date',
            'items' => 'Items',
            'subtotal' => 'Subtotal',
            'tax' => 'Tax',
            'discount' => 'Discount',
            'grand_total' => 'Grand Total',
            'status' => 'Status',
            'notes' => 'Notes',
            'terms_conditions' => 'Terms & Conditions',
        ],
        'status' => [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'received' => 'Received',
            'cancelled' => 'Cancelled',
        ],
        'messages' => [
            'create_success' => 'Purchase order created successfully.',
            'update_success' => 'Purchase order updated successfully.',
            'delete_success' => 'Purchase order deleted successfully.',
            'approved' => 'Purchase order approved.',
            'received' => 'Purchase order received.',
        ],
    ],

    // Stock Movement Module
    'stock_movements' => [
        'title' => 'Stock Movements',
        'singular' => 'Stock Movement',
        'description' => 'Track inventory stock movements',
        'fields' => [
            'movement_number' => 'Movement Number',
            'movement_date' => 'Movement Date',
            'movement_type' => 'Movement Type',
            'product' => 'Product',
            'quantity' => 'Quantity',
            'from_warehouse' => 'From Warehouse',
            'to_warehouse' => 'To Warehouse',
            'reference' => 'Reference',
            'notes' => 'Notes',
        ],
        'types' => [
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'return' => 'Return',
            'damage' => 'Damage',
        ],
        'messages' => [
            'create_success' => 'Stock movement recorded successfully.',
            'update_success' => 'Stock movement updated successfully.',
            'delete_success' => 'Stock movement deleted successfully.',
        ],
    ],

    // Employee Module
    'employees' => [
        'title' => 'Employees',
        'singular' => 'Employee',
        'description' => 'Manage employee information',
        'fields' => [
            'employee_code' => 'Employee Code',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'full_name' => 'Full Name',
            'id_number' => 'ID Number',
            'birth_date' => 'Birth Date',
            'birth_place' => 'Birth Place',
            'gender' => 'Gender',
            'marital_status' => 'Marital Status',
            'religion' => 'Religion',
            'blood_type' => 'Blood Type',
            'department' => 'Department',
            'position' => 'Position',
            'employment_type' => 'Employment Type',
            'hire_date' => 'Hire Date',
            'end_date' => 'End Date',
            'salary' => 'Salary',
            'bank_account' => 'Bank Account',
            'bank_name' => 'Bank Name',
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => 'Emergency Phone',
        ],
        'gender' => [
            'male' => 'Male',
            'female' => 'Female',
        ],
        'employment_type' => [
            'permanent' => 'Permanent',
            'contract' => 'Contract',
            'probation' => 'Probation',
            'internship' => 'Internship',
        ],
        'messages' => [
            'create_success' => 'Employee created successfully.',
            'update_success' => 'Employee updated successfully.',
            'delete_success' => 'Employee deleted successfully.',
        ],
    ],

    // Attendance Module
    'attendances' => [
        'title' => 'Attendances',
        'singular' => 'Attendance',
        'description' => 'Track employee attendance',
        'fields' => [
            'employee' => 'Employee',
            'date' => 'Date',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
            'work_hours' => 'Work Hours',
            'overtime_hours' => 'Overtime Hours',
            'status' => 'Status',
            'notes' => 'Notes',
        ],
        'status' => [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'on_leave' => 'On Leave',
            'sick' => 'Sick',
            'half_day' => 'Half Day',
        ],
        'messages' => [
            'create_success' => 'Attendance recorded successfully.',
            'update_success' => 'Attendance updated successfully.',
            'delete_success' => 'Attendance deleted successfully.',
            'check_in_success' => 'Check in recorded successfully.',
            'check_out_success' => 'Check out recorded successfully.',
        ],
    ],

    // Account Module
    'accounts' => [
        'title' => 'Accounts',
        'singular' => 'Account',
        'description' => 'Manage chart of accounts',
        'fields' => [
            'account_code' => 'Account Code',
            'account_name' => 'Account Name',
            'account_type' => 'Account Type',
            'parent_account' => 'Parent Account',
            'normal_balance' => 'Normal Balance',
            'is_active' => 'Active',
            'is_default' => 'Default',
        ],
        'types' => [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expense',
        ],
        'balance' => [
            'debit' => 'Debit',
            'credit' => 'Credit',
        ],
        'messages' => [
            'create_success' => 'Account created successfully.',
            'update_success' => 'Account updated successfully.',
            'delete_success' => 'Account deleted successfully.',
        ],
    ],

    // Journal Entry Module
    'journal_entries' => [
        'title' => 'Journal Entries',
        'singular' => 'Journal Entry',
        'description' => 'Manage accounting journal entries',
        'fields' => [
            'entry_number' => 'Entry Number',
            'entry_date' => 'Entry Date',
            'reference' => 'Reference',
            'description' => 'Description',
            'lines' => 'Journal Lines',
            'account' => 'Account',
            'debit' => 'Debit',
            'credit' => 'Credit',
            'total_debit' => 'Total Debit',
            'total_credit' => 'Total Credit',
            'status' => 'Status',
            'posted_date' => 'Posted Date',
            'posted_by' => 'Posted By',
        ],
        'status' => [
            'draft' => 'Draft',
            'posted' => 'Posted',
            'void' => 'Void',
        ],
        'messages' => [
            'create_success' => 'Journal entry created successfully.',
            'update_success' => 'Journal entry updated successfully.',
            'delete_success' => 'Journal entry deleted successfully.',
            'posted' => 'Journal entry posted successfully.',
            'unposted' => 'Journal entry unposted successfully.',
            'void' => 'Journal entry voided successfully.',
            'unbalanced' => 'Debit and credit must be balanced.',
        ],
    ],

    // User Module
    'users' => [
        'title' => 'Users',
        'singular' => 'User',
        'description' => 'Manage system users',
        'fields' => [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password Confirmation',
            'role' => 'Role',
            'is_active' => 'Active',
            'last_login' => 'Last Login',
        ],
        'messages' => [
            'create_success' => 'User created successfully.',
            'update_success' => 'User updated successfully.',
            'delete_success' => 'User deleted successfully.',
            'password_changed' => 'Password changed successfully.',
        ],
    ],

];
