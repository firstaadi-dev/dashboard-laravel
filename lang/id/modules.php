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
        'title' => 'Pelanggan',
        'singular' => 'Pelanggan',
        'description' => 'Kelola informasi dan hubungan pelanggan',
        'fields' => [
            'customer_code' => 'Kode Pelanggan',
            'company_name' => 'Nama Perusahaan',
            'contact_person' => 'Kontak Person',
            'tax_number' => 'NPWP',
            'credit_limit' => 'Batas Kredit',
            'payment_terms' => 'Termin Pembayaran',
            'website' => 'Website',
        ],
        'messages' => [
            'create_success' => 'Pelanggan berhasil dibuat.',
            'update_success' => 'Pelanggan berhasil diperbarui.',
            'delete_success' => 'Pelanggan berhasil dihapus.',
        ],
    ],

    // Supplier Module
    'suppliers' => [
        'title' => 'Pemasok',
        'singular' => 'Pemasok',
        'description' => 'Kelola informasi dan hubungan pemasok',
        'fields' => [
            'supplier_code' => 'Kode Pemasok',
            'company_name' => 'Nama Perusahaan',
            'contact_person' => 'Kontak Person',
            'tax_number' => 'NPWP',
            'bank_account' => 'Nomor Rekening',
            'bank_name' => 'Nama Bank',
            'payment_terms' => 'Termin Pembayaran',
            'website' => 'Website',
        ],
        'messages' => [
            'create_success' => 'Pemasok berhasil dibuat.',
            'update_success' => 'Pemasok berhasil diperbarui.',
            'delete_success' => 'Pemasok berhasil dihapus.',
        ],
    ],

    // Product Module
    'products' => [
        'title' => 'Produk',
        'singular' => 'Produk',
        'description' => 'Kelola persediaan dan informasi produk',
        'fields' => [
            'product_code' => 'Kode Produk',
            'product_name' => 'Nama Produk',
            'sku' => 'SKU',
            'barcode' => 'Barcode',
            'category' => 'Kategori',
            'unit' => 'Satuan',
            'purchase_price' => 'Harga Beli',
            'selling_price' => 'Harga Jual',
            'stock' => 'Stok',
            'min_stock' => 'Stok Minimum',
            'max_stock' => 'Stok Maksimum',
            'reorder_level' => 'Level Pemesanan Ulang',
            'weight' => 'Berat',
            'dimensions' => 'Dimensi',
            'is_active' => 'Aktif',
        ],
        'messages' => [
            'create_success' => 'Produk berhasil dibuat.',
            'update_success' => 'Produk berhasil diperbarui.',
            'delete_success' => 'Produk berhasil dihapus.',
            'low_stock_warning' => 'Stok produk menipis.',
            'out_of_stock' => 'Produk habis.',
        ],
    ],

    // Category Module
    'categories' => [
        'title' => 'Kategori',
        'singular' => 'Kategori',
        'description' => 'Kelola kategori produk',
        'fields' => [
            'category_name' => 'Nama Kategori',
            'parent_category' => 'Kategori Induk',
            'sort_order' => 'Urutan',
            'is_active' => 'Aktif',
        ],
        'messages' => [
            'create_success' => 'Kategori berhasil dibuat.',
            'update_success' => 'Kategori berhasil diperbarui.',
            'delete_success' => 'Kategori berhasil dihapus.',
        ],
    ],

    // Transaction Module
    'transactions' => [
        'title' => 'Transaksi',
        'singular' => 'Transaksi',
        'description' => 'Kelola transaksi penjualan',
        'fields' => [
            'transaction_number' => 'Nomor Transaksi',
            'transaction_date' => 'Tanggal Transaksi',
            'customer' => 'Pelanggan',
            'items' => 'Item',
            'subtotal' => 'Subtotal',
            'tax' => 'Pajak',
            'discount' => 'Diskon',
            'grand_total' => 'Total Keseluruhan',
            'payment_method' => 'Metode Pembayaran',
            'payment_status' => 'Status Pembayaran',
            'paid_amount' => 'Jumlah Dibayar',
            'change' => 'Kembalian',
            'notes' => 'Catatan',
        ],
        'messages' => [
            'create_success' => 'Transaksi berhasil dibuat.',
            'update_success' => 'Transaksi berhasil diperbarui.',
            'delete_success' => 'Transaksi berhasil dihapus.',
            'payment_completed' => 'Pembayaran berhasil diselesaikan.',
        ],
    ],

    // Purchase Order Module
    'purchase_orders' => [
        'title' => 'Pesanan Pembelian',
        'singular' => 'Pesanan Pembelian',
        'description' => 'Kelola pesanan pembelian',
        'fields' => [
            'po_number' => 'Nomor PO',
            'po_date' => 'Tanggal PO',
            'supplier' => 'Pemasok',
            'expected_delivery_date' => 'Tanggal Pengiriman Diharapkan',
            'items' => 'Item',
            'subtotal' => 'Subtotal',
            'tax' => 'Pajak',
            'discount' => 'Diskon',
            'grand_total' => 'Total Keseluruhan',
            'status' => 'Status',
            'notes' => 'Catatan',
            'terms_conditions' => 'Syarat & Ketentuan',
        ],
        'status' => [
            'draft' => 'Draft',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'received' => 'Diterima',
            'cancelled' => 'Dibatalkan',
        ],
        'messages' => [
            'create_success' => 'Pesanan pembelian berhasil dibuat.',
            'update_success' => 'Pesanan pembelian berhasil diperbarui.',
            'delete_success' => 'Pesanan pembelian berhasil dihapus.',
            'approved' => 'Pesanan pembelian disetujui.',
            'received' => 'Pesanan pembelian diterima.',
        ],
    ],

    // Stock Movement Module
    'stock_movements' => [
        'title' => 'Pergerakan Stok',
        'singular' => 'Pergerakan Stok',
        'description' => 'Lacak pergerakan stok persediaan',
        'fields' => [
            'movement_number' => 'Nomor Pergerakan',
            'movement_date' => 'Tanggal Pergerakan',
            'movement_type' => 'Tipe Pergerakan',
            'product' => 'Produk',
            'quantity' => 'Kuantitas',
            'from_warehouse' => 'Dari Gudang',
            'to_warehouse' => 'Ke Gudang',
            'reference' => 'Referensi',
            'notes' => 'Catatan',
        ],
        'types' => [
            'purchase' => 'Pembelian',
            'sale' => 'Penjualan',
            'adjustment' => 'Penyesuaian',
            'transfer' => 'Transfer',
            'return' => 'Retur',
            'damage' => 'Kerusakan',
        ],
        'messages' => [
            'create_success' => 'Pergerakan stok berhasil dicatat.',
            'update_success' => 'Pergerakan stok berhasil diperbarui.',
            'delete_success' => 'Pergerakan stok berhasil dihapus.',
        ],
    ],

    // Employee Module
    'employees' => [
        'title' => 'Karyawan',
        'singular' => 'Karyawan',
        'description' => 'Kelola informasi karyawan',
        'fields' => [
            'employee_code' => 'Kode Karyawan',
            'first_name' => 'Nama Depan',
            'last_name' => 'Nama Belakang',
            'full_name' => 'Nama Lengkap',
            'id_number' => 'NIK',
            'birth_date' => 'Tanggal Lahir',
            'birth_place' => 'Tempat Lahir',
            'gender' => 'Jenis Kelamin',
            'marital_status' => 'Status Pernikahan',
            'religion' => 'Agama',
            'blood_type' => 'Golongan Darah',
            'department' => 'Departemen',
            'position' => 'Jabatan',
            'employment_type' => 'Tipe Kepegawaian',
            'hire_date' => 'Tanggal Bergabung',
            'end_date' => 'Tanggal Keluar',
            'salary' => 'Gaji',
            'bank_account' => 'Nomor Rekening',
            'bank_name' => 'Nama Bank',
            'emergency_contact' => 'Kontak Darurat',
            'emergency_phone' => 'Telepon Darurat',
        ],
        'gender' => [
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
        ],
        'employment_type' => [
            'permanent' => 'Tetap',
            'contract' => 'Kontrak',
            'probation' => 'Percobaan',
            'internship' => 'Magang',
        ],
        'messages' => [
            'create_success' => 'Karyawan berhasil dibuat.',
            'update_success' => 'Karyawan berhasil diperbarui.',
            'delete_success' => 'Karyawan berhasil dihapus.',
        ],
    ],

    // Attendance Module
    'attendances' => [
        'title' => 'Kehadiran',
        'singular' => 'Kehadiran',
        'description' => 'Lacak kehadiran karyawan',
        'fields' => [
            'employee' => 'Karyawan',
            'date' => 'Tanggal',
            'check_in' => 'Masuk',
            'check_out' => 'Keluar',
            'work_hours' => 'Jam Kerja',
            'overtime_hours' => 'Jam Lembur',
            'status' => 'Status',
            'notes' => 'Catatan',
        ],
        'status' => [
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'on_leave' => 'Cuti',
            'sick' => 'Sakit',
            'half_day' => 'Setengah Hari',
        ],
        'messages' => [
            'create_success' => 'Kehadiran berhasil dicatat.',
            'update_success' => 'Kehadiran berhasil diperbarui.',
            'delete_success' => 'Kehadiran berhasil dihapus.',
            'check_in_success' => 'Berhasil mencatat waktu masuk.',
            'check_out_success' => 'Berhasil mencatat waktu keluar.',
        ],
    ],

    // Account Module
    'accounts' => [
        'title' => 'Akun',
        'singular' => 'Akun',
        'description' => 'Kelola bagan akun',
        'fields' => [
            'account_code' => 'Kode Akun',
            'account_name' => 'Nama Akun',
            'account_type' => 'Tipe Akun',
            'parent_account' => 'Akun Induk',
            'normal_balance' => 'Saldo Normal',
            'is_active' => 'Aktif',
            'is_default' => 'Default',
        ],
        'types' => [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
        ],
        'balance' => [
            'debit' => 'Debit',
            'credit' => 'Kredit',
        ],
        'messages' => [
            'create_success' => 'Akun berhasil dibuat.',
            'update_success' => 'Akun berhasil diperbarui.',
            'delete_success' => 'Akun berhasil dihapus.',
        ],
    ],

    // Journal Entry Module
    'journal_entries' => [
        'title' => 'Jurnal Umum',
        'singular' => 'Jurnal Umum',
        'description' => 'Kelola jurnal akuntansi',
        'fields' => [
            'entry_number' => 'Nomor Jurnal',
            'entry_date' => 'Tanggal Jurnal',
            'reference' => 'Referensi',
            'description' => 'Deskripsi',
            'lines' => 'Baris Jurnal',
            'account' => 'Akun',
            'debit' => 'Debit',
            'credit' => 'Kredit',
            'total_debit' => 'Total Debit',
            'total_credit' => 'Total Kredit',
            'status' => 'Status',
            'posted_date' => 'Tanggal Posting',
            'posted_by' => 'Diposting Oleh',
        ],
        'status' => [
            'draft' => 'Draft',
            'posted' => 'Diposting',
            'void' => 'Dibatalkan',
        ],
        'messages' => [
            'create_success' => 'Jurnal umum berhasil dibuat.',
            'update_success' => 'Jurnal umum berhasil diperbarui.',
            'delete_success' => 'Jurnal umum berhasil dihapus.',
            'posted' => 'Jurnal umum berhasil diposting.',
            'unposted' => 'Jurnal umum berhasil dibatalkan postingnya.',
            'void' => 'Jurnal umum berhasil dibatalkan.',
            'unbalanced' => 'Debit dan kredit harus seimbang.',
        ],
    ],

    // User Module
    'users' => [
        'title' => 'Pengguna',
        'singular' => 'Pengguna',
        'description' => 'Kelola pengguna sistem',
        'fields' => [
            'username' => 'Nama Pengguna',
            'email' => 'Email',
            'password' => 'Kata Sandi',
            'password_confirmation' => 'Konfirmasi Kata Sandi',
            'role' => 'Peran',
            'is_active' => 'Aktif',
            'last_login' => 'Login Terakhir',
        ],
        'messages' => [
            'create_success' => 'Pengguna berhasil dibuat.',
            'update_success' => 'Pengguna berhasil diperbarui.',
            'delete_success' => 'Pengguna berhasil dihapus.',
            'password_changed' => 'Kata sandi berhasil diubah.',
        ],
    ],

];
