# Fitur-Fitur ERP Dashboard Laravel

Dokumen ini menjelaskan semua fitur ERP yang telah ditambahkan ke sistem Dashboard Laravel.

## 📋 Daftar Isi

1. [Role-Based Access Control (RBAC)](#role-based-access-control-rbac)
2. [Modul-Modul ERP](#modul-modul-erp)
3. [Dashboard & Widgets](#dashboard--widgets)
4. [Struktur Database](#struktur-database)
5. [Navigasi & Menu](#navigasi--menu)
6. [Cara Setup & Penggunaan](#cara-setup--penggunaan)

---

## 🔐 Role-Based Access Control (RBAC)

Sistem menggunakan **Spatie Laravel Permission** untuk mengelola hak akses berbasis role.

### Roles yang Tersedia:

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Super Admin** | Akses penuh ke semua fitur | ✅ Semua modul dan manajemen role |
| **Admin** | Hampir full access | ✅ Semua modul (kecuali manajemen role) |
| **Manager** | Supervisi & approval | ✅ View semua, approve transaksi & PO, manage attendance |
| **Cashier** | Operasional penjualan | ✅ Transaksi, Customer, View Products |
| **Staff** | Akses terbatas | ✅ View only untuk operasional |

### Login Default:
```
Email: admin@example.com
Password: password
Role: Super Admin
```

---

## 🏢 Modul-Modul ERP

### 1. **CRM (Customer Relationship Management)**
📁 Menu Group: `CRM`

#### Customer Management
- ✅ Data customer (individual & company)
- ✅ Credit limit tracking
- ✅ Current balance monitoring
- ✅ Informasi kontak lengkap (email, phone, address)
- ✅ Tax ID (NPWP)
- ✅ Status aktif/non-aktif

**Permissions:**
- `view_customers`
- `create_customers`
- `edit_customers`
- `delete_customers`

---

### 2. **Sales (Penjualan)**
📁 Menu Group: `Sales`

#### Transaction Management
- ✅ Transaksi penjualan dengan auto-numbering
- ✅ Multi-item per transaksi (repeater)
- ✅ Auto-calculation subtotal & total
- ✅ Multiple payment methods (Cash, Transfer, Debit, Credit, E-Wallet)
- ✅ Status tracking (Pending, Completed, Cancelled)
- ✅ Link ke customer
- ✅ Soft delete support

**Permissions:**
- `view_transactions`
- `create_transactions`
- `edit_transactions`
- `delete_transactions`
- `approve_transactions`

---

### 3. **Inventory (Gudang & Stok)**
📁 Menu Group: `Inventory`

#### Product Management
- ✅ Product catalog dengan SKU unik
- ✅ Category organization
- ✅ Stock level tracking
- ✅ Unit name (pcs, kg, liter, dll)
- ✅ Pricing management
- ✅ Low stock & out of stock indicators
- ✅ Soft delete support

#### Category Management
- ✅ Product categorization
- ✅ Product count per category

#### Stock Movement
- ✅ Track semua pergerakan stok
- ✅ Tipe: In, Out, Adjustment, Transfer
- ✅ Reference ke PO atau Transaction
- ✅ Audit trail lengkap (previous stock, new stock, quantity)
- ✅ User tracking (siapa yang melakukan)

**Permissions:**
- `view_products`, `create_products`, `edit_products`, `delete_products`
- `view_stock_movements`, `create_stock_movements`, `adjust_stock`

---

### 4. **Procurement (Pengadaan)**
📁 Menu Group: `Procurement`

#### Supplier Management
- ✅ Data supplier lengkap
- ✅ Contact person tracking
- ✅ Payment terms (COD, Net 7-60 days)
- ✅ Credit balance monitoring
- ✅ Tax ID (NPWP)
- ✅ Status aktif/non-aktif

#### Purchase Order (PO)
- ✅ PO creation dengan auto-numbering
- ✅ Multi-item per PO
- ✅ Expected delivery date tracking
- ✅ Received date tracking
- ✅ Status workflow (Draft → Submitted → Approved → Received)
- ✅ Tax, discount, shipping cost calculation
- ✅ Link ke supplier
- ✅ Soft delete support

**Permissions:**
- `view_suppliers`, `create_suppliers`, `edit_suppliers`, `delete_suppliers`
- `view_purchase_orders`, `create_purchase_orders`, `approve_purchase_orders`

---

### 5. **Human Resources (SDM)**
📁 Menu Group: `Human Resources`

#### Employee Management
- ✅ Employee data lengkap
- ✅ Employee code unik
- ✅ Personal info (nama, email, phone, birth date, gender)
- ✅ Address information
- ✅ ID Card & Tax ID (KTP, NPWP)
- ✅ Employment details (position, department, status)
- ✅ Join date & end date tracking
- ✅ Basic salary management
- ✅ Link ke user account (optional)
- ✅ Soft delete support

#### Attendance Management
- ✅ Daily attendance tracking
- ✅ Clock in/out time recording
- ✅ Status (Present, Absent, Late, Sick, Permission, Holiday)
- ✅ Work hours calculation (dalam menit)
- ✅ Overtime hours tracking
- ✅ Unique constraint per employee per day

**Permissions:**
- `view_employees`, `create_employees`, `edit_employees`, `delete_employees`
- `view_attendances`, `create_attendances`, `edit_attendances`

---

### 6. **Accounting (Akuntansi)**
📁 Menu Group: `Accounting`

#### Chart of Accounts
- ✅ Account hierarchy (parent-child)
- ✅ Account types (Asset, Liability, Equity, Revenue, Expense)
- ✅ Subtypes untuk klasifikasi detail
- ✅ Normal balance (Debit/Credit)
- ✅ Current balance tracking
- ✅ Active/inactive status
- ✅ Soft delete support

#### Journal Entries
- ✅ General journal dengan auto-numbering
- ✅ Multiple lines per entry (debit & credit)
- ✅ Reference tracking ke Transaction/PO/dll (polymorphic)
- ✅ Status workflow (Draft → Posted → Reversed)
- ✅ Posted date tracking
- ✅ User tracking
- ✅ Soft delete support

**Permissions:**
- `view_accounts`, `create_accounts`, `edit_accounts`
- `view_journal_entries`, `create_journal_entries`, `post_journal_entries`

---

## 📊 Dashboard & Widgets

### Stats Overview Widget

Dashboard menampilkan statistik real-time yang disesuaikan dengan role user:

#### Untuk Cashier & Above:
- 💰 **Sales Hari Ini** - Total penjualan completed hari ini
- 📈 **Sales Bulan Ini** - Total penjualan bulan berjalan

#### Untuk Staff & Above:
- ⚠️ **Stok Rendah** - Produk dengan stok ≤ 10
- ❌ **Stok Habis** - Produk dengan stok 0

#### Untuk Cashier & Above:
- 👥 **Customer Aktif** - Total customer terdaftar

#### Untuk Manager & Above:
- 📄 **PO Menunggu** - Purchase order perlu approval
- 👨‍💼 **Karyawan Aktif** - Total karyawan terdaftar

**Widget ini menggunakan permission-based visibility** sehingga setiap role hanya melihat statistik yang relevan.

---

## 🗄️ Struktur Database

### Tabel Baru yang Ditambahkan:

1. **roles** - Roles untuk RBAC
2. **permissions** - Permissions detail
3. **model_has_roles** - Mapping user ke roles
4. **model_has_permissions** - Mapping user ke permissions
5. **role_has_permissions** - Mapping role ke permissions
6. **customers** - Data customer/pelanggan
7. **suppliers** - Data supplier/pemasok
8. **purchase_orders** - Purchase orders
9. **purchase_order_items** - Item dalam PO
10. **stock_movements** - Audit trail pergerakan stok
11. **employees** - Data karyawan
12. **attendances** - Absensi karyawan
13. **accounts** - Chart of accounts
14. **journal_entries** - Journal entries header
15. **journal_entry_lines** - Journal entry detail lines

### Relationships Antar Tabel:

```
Users ──┬── Transactions (created by)
        ├── PurchaseOrders (created by)
        ├── StockMovements (recorded by)
        ├── JournalEntries (created by)
        └── Employees (optional link)

Categories ──── Products

Products ──┬── TransactionItems
           ├── PurchaseOrderItems
           └── StockMovements

Customers ──── Transactions

Suppliers ──── PurchaseOrders

Employees ──── Attendances

Accounts ──┬── JournalEntryLines
           ├── parent (self-reference)
           └── children (self-reference)

JournalEntries ──── JournalEntryLines
```

---

## 🎨 Navigasi & Menu

Menu sidebar diorganisir dalam **Navigation Groups** untuk kemudahan akses:

### 📌 CRM
- Customers

### 📌 Sales
- Transactions (Transaksi)

### 📌 Inventory
- Products
- Categories
- Stock Movements

### 📌 Procurement
- Suppliers
- Purchase Orders

### 📌 Human Resources
- Employees
- Attendances

### 📌 Accounting
- Chart of Accounts
- Journal Entries

### 📌 System (Super Admin only)
- Users
- Roles & Permissions

**Setiap menu item memiliki icon Heroicon yang intuitif dan hanya tampil jika user memiliki permission yang sesuai.**

---

## 🚀 Cara Setup & Penggunaan

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dashboard_laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
```

Ini akan:
- Membuat semua tabel
- Setup roles & permissions
- Membuat user Super Admin (admin@example.com / password)

### 5. Build Assets
```bash
npm run dev
# atau untuk production:
npm run build
```

### 6. Start Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000/admin`

### 7. Login
```
Email: admin@example.com
Password: password
```

---

## 🔧 Membuat User dengan Role Tertentu

### Via Tinker:
```bash
php artisan tinker
```

```php
// Create user
$user = \App\Models\User::create([
    'name' => 'Kasir 1',
    'email' => 'kasir@example.com',
    'password' => bcrypt('password')
]);

// Assign role
$user->assignRole('cashier');

// Check permissions
$user->can('view_transactions'); // true
$user->can('delete_products'); // false
```

### Via Filament Admin Panel:
1. Login sebagai Super Admin
2. Go to Users menu
3. Create new user
4. Assign role dari dropdown
5. Save

---

## 📈 Best Practices

### 1. Permission Checking
Selalu gunakan middleware atau manual check:
```php
// In controller
if ($user->can('create_products')) {
    // Allow action
}

// In Blade
@can('edit_customers')
    <button>Edit</button>
@endcan
```

### 2. Navigation Visibility
Resource sudah otomatis menggunakan permission check:
```php
public static function canViewAny(): bool
{
    return auth()->user()->can('view_customers');
}
```

### 3. Soft Deletes
Gunakan soft delete untuk data penting agar bisa di-restore:
```php
// Restore deleted record
$customer = Customer::withTrashed()->find($id);
$customer->restore();

// Force delete permanently
$customer->forceDelete();
```

---

## 🎯 Roadmap / Fitur yang Bisa Ditambahkan

- [ ] **Reports Module** - Sales, Inventory, Financial reports
- [ ] **Email Notifications** - Order confirmations, low stock alerts
- [ ] **Barcode/QR Scanning** - Untuk inventory management
- [ ] **Multi-warehouse** - Support untuk multiple gudang
- [ ] **Payroll** - Perhitungan gaji karyawan
- [ ] **Tax Calculation** - PPN/PPh automation
- [ ] **API Integration** - REST API untuk mobile app
- [ ] **Advanced Analytics** - Charts, graphs, forecasting
- [ ] **Document Management** - Upload & attach files
- [ ] **Approval Workflow** - Multi-level approval process

---

## 📝 Catatan Teknis

### Tech Stack:
- **Laravel 12** - Backend framework
- **Filament 4** - Admin panel
- **Spatie Laravel Permission** - RBAC
- **Tailwind CSS 4** - Styling
- **Vite 7** - Asset bundling

### Key Files:
- **Migrations**: `database/migrations/`
- **Models**: `app/Models/`
- **Filament Resources**: `app/Filament/Resources/`
- **Seeders**: `database/seeders/RolePermissionSeeder.php`
- **Widgets**: `app/Filament/Widgets/StatsOverview.php`

---

## 🆘 Troubleshooting

### Problem: Permission tidak berfungsi
**Solution:**
```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### Problem: Navigasi tidak muncul
**Solution:**
Pastikan user sudah login dan memiliki role:
```php
$user->roles; // Check roles
$user->getAllPermissions(); // Check permissions
```

### Problem: Widget tidak muncul
**Solution:**
Register widget di `AdminPanelProvider`:
```php
->widgets([
    \App\Filament\Widgets\StatsOverview::class,
])
```

---

## 👨‍💻 Developer Notes

Sistem ini dirancang modular dan scalable. Setiap modul (Customer, Product, dll) memiliki:
1. **Model** dengan fillable, casts, relationships
2. **Migration** dengan foreign keys dan indexes
3. **Filament Resource** dengan form, table, filters
4. **Permissions** untuk akses control

**Untuk menambah modul baru**, ikuti pattern yang sama dan tambahkan permission di `RolePermissionSeeder`.

---

## 📄 License

This ERP system is part of Dashboard Laravel project.

---

**Dibuat dengan ❤️ menggunakan Laravel & Filament**
