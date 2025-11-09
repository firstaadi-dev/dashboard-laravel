<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Customer::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Type',
            'Company',
            'Email',
            'Phone',
            'Address',
            'City',
            'Province',
            'Postal Code',
            'Tax ID',
            'Credit Limit',
            'Current Balance',
            'Is Active',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->code,
            $customer->name,
            ucfirst($customer->type),
            $customer->company,
            $customer->email,
            $customer->phone,
            $customer->address,
            $customer->city,
            $customer->province,
            $customer->postal_code,
            $customer->tax_id,
            $customer->credit_limit,
            $customer->current_balance,
            $customer->is_active ? 'Yes' : 'No',
            $customer->notes,
            $customer->created_at?->format('Y-m-d H:i:s'),
            $customer->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
