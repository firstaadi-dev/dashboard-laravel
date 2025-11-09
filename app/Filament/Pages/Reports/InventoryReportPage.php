<?php

namespace App\Filament\Pages\Reports;

use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class InventoryReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static string $view = 'filament.pages.reports.inventory-report-page';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Inventory Report';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_inventory_reports') || $user->hasRole('super_admin'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn () => $this->exportToPdf()),
        ];
    }

    public function getInventoryData(): array
    {
        $products = Product::with(['category'])
            ->orderBy('stock', 'asc')
            ->get();

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->stock * $product->price;
        });

        $lowStockProducts = $products->filter(function ($product) {
            return $product->stock <= ($product->min_stock ?? 20);
        });

        $outOfStockProducts = $products->where('stock', 0);

        // Stock movements in last 30 days
        $stockMovements = StockMovement::where('movement_date', '>=', Carbon::now()->subDays(30))
            ->with('product')
            ->orderBy('movement_date', 'desc')
            ->limit(50)
            ->get();

        // Stock in vs out
        $stockIn = StockMovement::where('type', 'in')
            ->where('movement_date', '>=', Carbon::now()->subDays(30))
            ->sum('quantity');

        $stockOut = StockMovement::where('type', 'out')
            ->where('movement_date', '>=', Carbon::now()->subDays(30))
            ->sum('quantity');

        return [
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'low_stock_count' => $lowStockProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
            'products' => $products,
            'low_stock_products' => $lowStockProducts,
            'stock_movements' => $stockMovements,
            'stock_in_30_days' => $stockIn,
            'stock_out_30_days' => $stockOut,
        ];
    }

    public function exportToPdf()
    {
        $data = $this->getInventoryData();

        $pdf = Pdf::loadView('pdf.inventory-report', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'inventory-report-' . date('Y-m-d') . '.pdf');
    }
}
