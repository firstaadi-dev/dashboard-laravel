<?php

namespace App\Exports;

use App\Models\JournalEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JournalEntriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return JournalEntry::with(['lines.account', 'creator'])
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Entry Number',
            'Entry Date',
            'Type',
            'Reference',
            'Total Debit',
            'Total Credit',
            'Status',
            'Description',
            'Created By',
            'Created At',
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->entry_number,
            $entry->entry_date?->format('Y-m-d'),
            ucfirst($entry->type),
            $entry->reference,
            $entry->lines->sum('debit'),
            $entry->lines->sum('credit'),
            $this->formatStatus($entry->status),
            $entry->description,
            $entry->creator?->name,
            $entry->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'posted' => 'Posted',
            'void' => 'Void',
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
