<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Supplier::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Contact Person',
            'Email',
            'Phone',
            'Address',
            'City',
            'Province',
            'Postal Code',
            'Tax ID',
            'Payment Terms',
            'Credit Balance',
            'Is Active',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->code,
            $supplier->name,
            $supplier->contact_person,
            $supplier->email,
            $supplier->phone,
            $supplier->address,
            $supplier->city,
            $supplier->province,
            $supplier->postal_code,
            $supplier->tax_id,
            $supplier->payment_terms,
            $supplier->credit_balance,
            $supplier->is_active ? 'Yes' : 'No',
            $supplier->notes,
            $supplier->created_at?->format('Y-m-d H:i:s'),
            $supplier->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
