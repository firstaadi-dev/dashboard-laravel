<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Attendance::with(['employee', 'creator'])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee ID',
            'Employee Name',
            'Check In',
            'Check Out',
            'Work Hours',
            'Status',
            'Notes',
            'Created At',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date?->format('Y-m-d'),
            $attendance->employee?->employee_id,
            $attendance->employee?->name,
            $attendance->check_in?->format('H:i:s'),
            $attendance->check_out?->format('H:i:s'),
            $attendance->work_hours,
            $this->formatStatus($attendance->status),
            $attendance->notes,
            $attendance->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'on_leave' => 'On Leave',
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
