<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Base stats visible to all
        $stats = [];

        // Sales stats (visible to cashier, manager, admin, super_admin)
        if ($user->can('view_transactions')) {
            $todaySales = Transaction::whereDate('transaction_date', today())
                ->where('status', 'completed')
                ->sum('total_amount');

            $monthSales = Transaction::whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->where('status', 'completed')
                ->sum('total_amount');

            $stats[] = Stat::make('Sales Hari Ini', 'Rp ' . number_format($todaySales, 0, ',', '.'))
                ->description('Total penjualan hari ini')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success');

            $stats[] = Stat::make('Sales Bulan Ini', 'Rp ' . number_format($monthSales, 0, ',', '.'))
                ->description('Total penjualan bulan ' . now()->format('F'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info');
        }

        // Product/Inventory stats (visible to staff and above)
        if ($user->can('view_products')) {
            $lowStockProducts = Product::where('stock', '<=', 10)
                ->where('stock', '>', 0)
                ->count();

            $outOfStockProducts = Product::where('stock', '<=', 0)->count();

            $stats[] = Stat::make('Stok Rendah', $lowStockProducts)
                ->description('Produk dengan stok ≤ 10')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning');

            $stats[] = Stat::make('Stok Habis', $outOfStockProducts)
                ->description('Produk yang harus diorder')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger');
        }

        // Customer stats (visible to cashier and above)
        if ($user->can('view_customers')) {
            $activeCustomers = Customer::where('is_active', true)->count();

            $stats[] = Stat::make('Customer Aktif', $activeCustomers)
                ->description('Total customer terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('success');
        }

        // Purchase Order stats (visible to manager and above)
        if ($user->can('view_purchase_orders')) {
            $pendingPO = PurchaseOrder::whereIn('status', ['draft', 'submitted'])
                ->count();

            if ($pendingPO > 0) {
                $stats[] = Stat::make('PO Menunggu', $pendingPO)
                    ->description('Purchase order perlu persetujuan')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('warning');
            }
        }

        // Employee stats (visible to manager and above)
        if ($user->can('view_employees')) {
            $activeEmployees = Employee::where('is_active', true)->count();

            $stats[] = Stat::make('Karyawan Aktif', $activeEmployees)
                ->description('Total karyawan terdaftar')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info');
        }

        return $stats;
    }

    public static function canView(): bool
    {
        return Auth::check();
    }
}
