# Auto Journal Entries - Accounting Integration

Sistem ini memiliki fitur **automatic journal entry creation** yang terintegrasi dengan modul Transaksi, Purchase Order, dan Stock Movement untuk memastikan akuntansi yang akurat secara real-time.

---

## 📋 Daftar Isi

1. [Cara Kerja](#cara-kerja)
2. [Transaction Journal Entries](#transaction-journal-entries)
3. [Purchase Order Journal Entries](#purchase-order-journal-entries)
4. [Stock Movement Journal Entries](#stock-movement-journal-entries)
5. [Chart of Accounts](#chart-of-accounts)
6. [Troubleshooting](#troubleshooting)

---

## 🔄 Cara Kerja

Sistem menggunakan **Laravel Observers** yang otomatis mendeteksi perubahan pada model dan membuat journal entries sesuai dengan prinsip **double-entry bookkeeping** (debit = credit).

### Observers yang Aktif:

| Observer | Trigger | Action |
|----------|---------|--------|
| **TransactionObserver** | Transaction status → 'completed' | Create sales journal entry |
| **PurchaseOrderObserver** | PurchaseOrder status → 'received' | Create purchase journal entry |
| **StockMovementObserver** | StockMovement type = 'adjustment' | Create inventory adjustment entry |

Semua observers terdaftar di `app/Providers/AppServiceProvider.php`

---

## 💰 Transaction Journal Entries

### Trigger:
Ketika status Transaction berubah menjadi **'completed'**

### Journal Entry Created:

```
Entry Number: JE-YYYYMMDD-XXXXXX
Entry Date: [transaction_date]
Reference: Transaction #TRX-YYYYMMDD-XXXXXX
Status: Posted
```

### Accounting Entries:

| Account | Debit | Credit |
|---------|-------|--------|
| **Cash in Hand** atau **Bank Account** | Rp [total_amount] | - |
| **Sales Revenue** | - | Rp [total_amount] |

### Logika Payment Method:

- **Bank Transfer**, **Debit Card**, **Credit Card** → Debit ke **Bank Account**
- **Cash**, **E-Wallet**, **Other** → Debit ke **Cash in Hand**

### Contoh:

```
Transaction #TRX-20251109-ABC123
Customer: PT. Maju Jaya
Total: Rp 5.000.000
Payment: Bank Transfer
Status: Completed ✓

Jurnal Otomatis:
├─ Debit: Bank Account → Rp 5.000.000
└─ Credit: Sales Revenue → Rp 5.000.000
```

---

## 🛒 Purchase Order Journal Entries

### Trigger:
Ketika status PurchaseOrder berubah menjadi **'received'**

### Journal Entry Created:

```
Entry Number: JE-YYYYMMDD-XXXXXX
Entry Date: [received_date]
Reference: PurchaseOrder #PO-YYYYMMDD-XXXXXX
Status: Posted
```

### Accounting Entries:

| Account | Debit | Credit |
|---------|-------|--------|
| **Inventory** | Rp [total_amount] | - |
| **Accounts Payable** atau **Cash in Hand** | - | Rp [total_amount] |

### Logika Payment Terms:

**Credit Terms** (Net 7, Net 15, Net 30, Net 45, Net 60):
- Credit ke **Accounts Payable** (hutang ke supplier)

**Cash/Immediate Terms** (COD):
- Credit ke **Cash in Hand** (bayar langsung)

### Contoh:

```
Purchase Order #PO-20251109-XYZ789
Supplier: CV. Supplier Jaya
Total: Rp 10.000.000
Payment Terms: Net 30 (Kredit 30 hari)
Status: Received ✓

Jurnal Otomatis:
├─ Debit: Inventory → Rp 10.000.000
└─ Credit: Accounts Payable → Rp 10.000.000
```

---

## 📦 Stock Movement Journal Entries

### Trigger:
Ketika StockMovement dengan type **'adjustment'** dibuat

### Journal Entry Created:

```
Entry Number: JE-YYYYMMDD-XXXXXX
Entry Date: [movement_date]
Reference: StockMovement #SM-YYYYMMDD-XXXXXX
Status: Posted
```

### Accounting Entries:

#### Positive Adjustment (Tambah Stok):

| Account | Debit | Credit |
|---------|-------|--------|
| **Inventory** | Rp [quantity × price] | - |
| **Inventory Adjustment** | - | Rp [quantity × price] |

#### Negative Adjustment (Kurang Stok):

| Account | Debit | Credit |
|---------|-------|--------|
| **Inventory Adjustment** | Rp [quantity × price] | - |
| **Inventory** | - | Rp [quantity × price] |

### Contoh:

**Case 1: Stock Ditemukan (Positive Adjustment)**
```
Product: Laptop ASUS
Previous Stock: 100 unit
New Stock: 105 unit
Quantity: +5 unit
Price: Rp 5.000.000
Type: Adjustment

Jurnal Otomatis:
├─ Debit: Inventory → Rp 25.000.000
└─ Credit: Inventory Adjustment → Rp 25.000.000
```

**Case 2: Stock Hilang (Negative Adjustment)**
```
Product: Laptop ASUS
Previous Stock: 100 unit
New Stock: 95 unit
Quantity: -5 unit
Price: Rp 5.000.000
Type: Adjustment

Jurnal Otomatis:
├─ Debit: Inventory Adjustment → Rp 25.000.000
└─ Credit: Inventory → Rp 25.000.000
```

---

## 📊 Chart of Accounts

### Auto-Created Accounts

Observers akan otomatis membuat akun-akun berikut jika belum ada:

| Account Name | Type | Normal Balance | Auto Code | Description |
|--------------|------|----------------|-----------|-------------|
| **Cash in Hand** | Asset | Debit | 1001 | Kas di tangan |
| **Bank Account** | Asset | Debit | 1002 | Rekening bank perusahaan |
| **Inventory** | Asset | Debit | 1101 | Persediaan barang |
| **Accounts Payable** | Liability | Credit | 2001 | Hutang usaha ke supplier |
| **Sales Revenue** | Revenue | Credit | 4001 | Pendapatan dari penjualan |
| **Inventory Adjustment** | Expense | Debit | 5101 | Penyesuaian persediaan |

### Account Code Format:

```
1xxx = Assets (Aset)
2xxx = Liabilities (Kewajiban)
3xxx = Equity (Modal)
4xxx = Revenue (Pendapatan)
5xxx = Expense (Biaya)
```

### Automatic Numbering:

Sistem akan auto-increment dari akun terakhir dengan tipe yang sama:
- First Asset: 1000
- Second Asset: 1001
- Third Asset: 1002
- dst...

---

## 🔐 Fitur Keamanan

### 1. Duplicate Prevention

Observer mengecek apakah journal entry sudah ada untuk referensi yang sama:

```php
$existingEntry = JournalEntry::where('reference_type', 'App\Models\Transaction')
    ->where('reference_id', $transaction->id)
    ->first();

if ($existingEntry) {
    return; // Skip, sudah diproses
}
```

### 2. Database Transactions

Semua operasi dibungkus dalam DB transaction:

```php
DB::beginTransaction();
try {
    // Create journal entry
    // Create journal lines
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error("Failed: " . $e->getMessage());
}
```

### 3. Error Handling

Observer **TIDAK melempar exception** ke model utama, sehingga:
- Transaction tetap bisa completed meski jurnal gagal
- PO tetap bisa received meski jurnal gagal
- Stock movement tetap tercatat meski jurnal gagal

Error hanya di-log untuk investigasi.

---

## 🎯 Cara Menggunakan

### 1. Complete Transaction

Di Filament admin panel:
1. Buat Transaction baru
2. Tambahkan items
3. Set status = **'completed'**
4. Save

**Hasil:**
✅ Transaction tersimpan
✅ Journal Entry otomatis dibuat
✅ Check di menu **Accounting → Journal Entries**

### 2. Receive Purchase Order

Di Filament admin panel:
1. Buat PurchaseOrder baru
2. Tambahkan items
3. Set status = **'received'**
4. Isi received_date
5. Save

**Hasil:**
✅ PurchaseOrder tersimpan
✅ Journal Entry otomatis dibuat
✅ Inventory account di-debit
✅ Accounts Payable/Cash di-credit

### 3. Stock Adjustment

Di Filament admin panel:
1. Buat StockMovement baru
2. Pilih product
3. Set type = **'adjustment'**
4. Isi quantity (positive atau negative)
5. Save

**Hasil:**
✅ StockMovement tersimpan
✅ Journal Entry otomatis dibuat
✅ Inventory account disesuaikan
✅ Inventory Adjustment account di-debit/credit

---

## 🧪 Testing

### Test Transaction Journal:

```bash
php artisan tinker
```

```php
// Create transaction
$transaction = \App\Models\Transaction::create([
    'user_id' => 1,
    'transaction_number' => 'TRX-TEST-001',
    'transaction_date' => now(),
    'customer_name' => 'Test Customer',
    'total_amount' => 100000,
    'payment_method' => 'cash',
    'status' => 'pending'
]);

// Complete it (trigger observer)
$transaction->update(['status' => 'completed']);

// Check journal entry
\App\Models\JournalEntry::where('reference_type', 'App\Models\Transaction')
    ->where('reference_id', $transaction->id)
    ->with('lines.account')
    ->first();
```

### Verify Balance:

```php
$entry = \App\Models\JournalEntry::latest()->first();
$totalDebit = $entry->lines->sum('debit');
$totalCredit = $entry->lines->sum('credit');

echo "Debit: " . $totalDebit . "\n";
echo "Credit: " . $totalCredit . "\n";
echo "Balance: " . ($totalDebit - $totalCredit) . "\n"; // Should be 0
```

---

## 🛠️ Troubleshooting

### Problem: Journal entry tidak terbuat

**Solusi:**

1. **Check Logs:**
```bash
tail -f storage/logs/laravel.log
```

2. **Check Observer Registration:**
```php
// In app/Providers/AppServiceProvider.php
public function boot(): void
{
    Transaction::observe(TransactionObserver::class);
    PurchaseOrder::observe(PurchaseOrderObserver::class);
    StockMovement::observe(StockMovementObserver::class);
}
```

3. **Check Status:**
Pastikan status benar-benar berubah:
```php
$transaction->status; // Harus 'completed'
$transaction->isDirty('status'); // Harus true saat update
```

### Problem: Duplicate journal entries

**Solusi:**

Observer sudah punya duplicate check, tapi jika terjadi:

```bash
php artisan tinker
```

```php
// Delete duplicate
$duplicates = \App\Models\JournalEntry::where('reference_type', 'App\Models\Transaction')
    ->where('reference_id', 123)
    ->get();

// Keep first, delete rest
$duplicates->skip(1)->each->delete();
```

### Problem: Account tidak ditemukan

**Solusi:**

Observer auto-create accounts, tapi bisa dibuat manual:

```php
\App\Models\Account::create([
    'code' => '1001',
    'name' => 'Cash in Hand',
    'type' => 'asset',
    'normal_balance' => 'debit',
    'balance' => 0,
    'is_active' => true,
]);
```

### Problem: Balance tidak 0

**Solusi:**

Check journal lines:

```php
$entry = \App\Models\JournalEntry::find($id);
$entry->lines->each(function($line) {
    echo $line->account->name . " - ";
    echo "Debit: " . $line->debit . " | ";
    echo "Credit: " . $line->credit . "\n";
});
```

Jika ada masalah, delete dan trigger ulang observer.

---

## 📈 Best Practices

### 1. Selalu Review Journal Entries

Setelah complete transaction atau receive PO:
- Go to **Accounting → Journal Entries**
- Filter by date
- Verify debit = credit
- Check account mapping

### 2. Backup Database Sebelum Testing

```bash
php artisan db:backup
# atau
mysqldump -u root -p dashboard_laravel > backup.sql
```

### 3. Monitor Log Files

```bash
tail -f storage/logs/laravel.log | grep "Journal entry"
```

### 4. Periodic Balance Check

```bash
php artisan tinker
```

```php
// Check all unbalanced entries
$unbalanced = \App\Models\JournalEntry::all()->filter(function($entry) {
    $debit = $entry->lines->sum('debit');
    $credit = $entry->lines->sum('credit');
    return abs($debit - $credit) > 0.01; // Allow 1 cent difference
});

echo "Unbalanced entries: " . $unbalanced->count() . "\n";
```

---

## 🎓 Accounting Principles

Sistem ini mengikuti prinsip **Double-Entry Bookkeeping**:

### Aturan Dasar:

| Account Type | Increase | Decrease |
|--------------|----------|----------|
| **Asset** | Debit | Credit |
| **Liability** | Credit | Debit |
| **Equity** | Credit | Debit |
| **Revenue** | Credit | Debit |
| **Expense** | Debit | Credit |

### Contoh Transaksi Lengkap:

**Penjualan Tunai Rp 1.000.000:**
```
Debit: Cash in Hand → Rp 1.000.000 (Asset ↑)
Credit: Sales Revenue → Rp 1.000.000 (Revenue ↑)
```

**Pembelian Kredit Rp 2.000.000:**
```
Debit: Inventory → Rp 2.000.000 (Asset ↑)
Credit: Accounts Payable → Rp 2.000.000 (Liability ↑)
```

**Stock Hilang Rp 500.000:**
```
Debit: Inventory Adjustment → Rp 500.000 (Expense ↑)
Credit: Inventory → Rp 500.000 (Asset ↓)
```

---

## 📞 Support

Jika ada pertanyaan atau masalah dengan auto journal entries:

1. Check dokumentasi ini
2. Review observer code di `app/Observers/`
3. Check logs di `storage/logs/laravel.log`
4. Test dengan data dummy terlebih dahulu

---

**Auto-created by ERP Dashboard Laravel System**
**Last Updated: 2025-11-09**
