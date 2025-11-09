<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return PurchaseOrder::with(['supplier', 'items.product', 'creator'])
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'Order Date',
            'Expected Date',
            'Received Date',
            'Supplier',
            'Items Count',
            'Subtotal',
            'Tax',
            'Shipping Cost',
            'Total Amount',
            'Status',
            'Payment Status',
            'Payment Method',
            'Created By',
            'Notes',
            'Created At',
        ];
    }

    public function map($po): array
    {
        return [
            $po->po_number,
            $po->order_date?->format('Y-m-d'),
            $po->expected_date?->format('Y-m-d'),
            $po->received_date?->format('Y-m-d'),
            $po->supplier?->name,
            $po->items->count(),
            $po->subtotal,
            $po->tax,
            $po->shipping_cost,
            $po->total_amount,
            $this->formatStatus($po->status),
            $this->formatPaymentStatus($po->payment_status),
            $this->formatPaymentMethod($po->payment_method),
            $po->creator?->name,
            $po->notes,
            $po->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'received' => 'Received',
            'cancelled' => 'Cancelled',
            default => $status,
        };
    }

    private function formatPaymentStatus(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Belum Dibayar',
            'partial' => 'Dibayar Sebagian',
            'paid' => 'Lunas',
            default => $status,
        };
    }

    private function formatPaymentMethod(?string $method): string
    {
        if (!$method) {
            return '-';
        }

        return match ($method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'debit' => 'Debit',
            'credit' => 'Kredit',
            default => $method,
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
