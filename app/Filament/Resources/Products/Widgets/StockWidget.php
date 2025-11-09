<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StockWidget extends ChartWidget
{
    protected ?string $heading = "Stock Condition";
    protected function getData(): array
    {
        $stats = Product::query()
            ->select(
            // Logika ini HARUS SAMA PERSIS dengan logika status di tabel Anda
            // 1. Out of Stock: stock <= 0
                DB::raw('SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock'),

                // 2. Low Stock: stock > 0 AND stock <= 20
                DB::raw('SUM(CASE WHEN stock > 0 AND stock <= 20 THEN 1 ELSE 0 END) as low_stock'),

                // 3. In Stock: stock > 20 AND stock <= 50
                DB::raw('SUM(CASE WHEN stock > 20 AND stock <= 50 THEN 1 ELSE 0 END) as in_stock'),

                // 4. Well Stocked: stock > 50
                DB::raw('SUM(CASE WHEN stock > 50 THEN 1 ELSE 0 END) as well_stocked')
            )
            ->first();

        $counts = [
            $stats->out_of_stock ?? 0,
            $stats->low_stock ?? 0,
            $stats->in_stock ?? 0,
            $stats->well_stocked ?? 0,
        ];

        // Label untuk setiap status
        $labels = [
            'Out of Stock',
            'Low Stock',
            'In Stock',
            'Well Stocked',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Status Stok',
                    'data' => $counts,
                    // Warna ini dicocokkan dengan warna status Anda (danger, warning, success, info)
                    'backgroundColor' => [
                        'rgb(239, 68, 68)',    // danger
                        'rgb(245, 158, 11)',   // warning
                        'rgb(16, 185, 129)',   // success
                        'rgb(59, 130, 246)',   // info
                    ],
                    'borderColor' => 'rgb(255, 255, 255, 0.1)', // Garis pemisah antar slice
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom', // Posisikan legenda di bawah chart
                ],
                'tooltip' => [
                    'callbacks' => [
                        // Callback ini untuk kustomisasi tooltip saat hover
                        // Ini akan menampilkan: "Status: X Produk (Y.YY%)"
                        'label' => new RawJs('
                            function (context) {
                                let label = context.label || \'\';
                                if (label) {
                                    label += \': \';
                                }
                                if (context.parsed !== null) {
                                    // Ambil jumlah produk (count)
                                    let count = context.parsed;

                                    // Hitung total semua produk
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);

                                    // Hitung persentase
                                    let percentage = (count / total * 100).toFixed(2);

                                    // Format label: "10 Produk (15.00%)"
                                    label += count + \' Produk (\' + percentage + \'%)\';
                                }
                                return label;
                            }
                        '),
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false, // Penting agar maxHeight berfungsi
        ];
    }
}
