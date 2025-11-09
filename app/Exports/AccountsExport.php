<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Account::with(['parent', 'creator', 'updater'])
            ->orderBy('code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Type',
            'Parent Account',
            'Description',
            'Is Active',
            'Created At',
            'Updated At',
        ];
    }

    public function map($account): array
    {
        return [
            $account->code,
            $account->name,
            ucfirst($account->type),
            $account->parent?->name,
            $account->description,
            $account->is_active ? 'Yes' : 'No',
            $account->created_at?->format('Y-m-d H:i:s'),
            $account->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
