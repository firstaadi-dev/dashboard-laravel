<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Product::with(['category', 'creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Name',
            'Category',
            'Description',
            'Price',
            'Stock',
            'Unit',
            'Min Stock',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($product): array
    {
        $status = match (true) {
            $product->stock <= 0 => 'Out of Stock',
            $product->stock <= 20 => 'Low Stock',
            $product->stock <= 50 => 'In Stock',
            default => 'Well Stocked',
        };

        return [
            $product->SKU,
            $product->name,
            $product->category?->name,
            $product->description,
            $product->price,
            $product->stock,
            $product->unit_name,
            $product->min_stock,
            $status,
            $product->created_at?->format('Y-m-d H:i:s'),
            $product->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
