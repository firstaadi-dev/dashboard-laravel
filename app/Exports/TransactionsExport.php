<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Transaction::with(['items.product', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Transaction Number',
            'Date',
            'Customer Name',
            'Items Count',
            'Subtotal',
            'Tax',
            'Discount',
            'Total Amount',
            'Amount Paid',
            'Change',
            'Payment Method',
            'Status',
            'Created By',
            'Notes',
            'Created At',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_number,
            $transaction->transaction_date?->format('Y-m-d H:i:s'),
            $transaction->customer_name,
            $transaction->items->count(),
            $transaction->subtotal,
            $transaction->tax,
            $transaction->discount,
            $transaction->total_amount,
            $transaction->amount_paid,
            $transaction->change,
            $this->formatPaymentMethod($transaction->payment_method),
            $this->formatStatus($transaction->status),
            $transaction->user?->name,
            $transaction->notes,
            $transaction->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatPaymentMethod(string $method): string
    {
        return match ($method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer',
            'debit' => 'Debit',
            'credit' => 'Kredit',
            'e-wallet' => 'E-Wallet',
            default => $method,
        };
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
