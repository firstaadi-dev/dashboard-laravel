<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Employee::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Position',
            'Department',
            'Hire Date',
            'Salary',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->employee_id,
            $employee->name,
            $employee->email,
            $employee->phone,
            $employee->address,
            $employee->position,
            $employee->department,
            $employee->hire_date?->format('Y-m-d'),
            $employee->salary,
            $employee->status,
            $employee->created_at?->format('Y-m-d H:i:s'),
            $employee->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
