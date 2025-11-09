<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return StockMovement::with(['product', 'creator'])
            ->orderBy('movement_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Reference Number',
            'Movement Date',
            'Product',
            'Type',
            'Quantity',
            'Unit',
            'Unit Price',
            'Total Value',
            'Previous Stock',
            'New Stock',
            'Reference Type',
            'Reference ID',
            'Notes',
            'Created By',
            'Created At',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->reference_number,
            $movement->movement_date?->format('Y-m-d H:i:s'),
            $movement->product?->name,
            $this->formatType($movement->type),
            $movement->quantity,
            $movement->unit,
            $movement->unit_price,
            $movement->total_value,
            $movement->previous_stock,
            $movement->new_stock,
            $movement->reference_type,
            $movement->reference_id,
            $movement->notes,
            $movement->creator?->name,
            $movement->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatType(string $type): string
    {
        return match ($type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            default => $type,
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
