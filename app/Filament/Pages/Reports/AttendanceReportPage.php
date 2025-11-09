<?php

namespace App\Filament\Pages\Reports;

use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendancesExport;

class AttendanceReportPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.reports.attendance-report-page';

    protected static string|null|\UnitEnum $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Attendance Report';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public $startDate;
    public $endDate;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasPermissionTo('view_hr_reports') || $user->hasRole('super_admin'));
    }

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->form->fill([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->default(Carbon::now()->startOfMonth())
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->default(Carbon::now())
                    ->native(false),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate Report')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    $this->startDate = $this->data['start_date'];
                    $this->endDate = $this->data['end_date'];
                }),

            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn () => $this->exportToPdf()),

            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->exportToExcel()),
        ];
    }

    public function getAttendanceData(): array
    {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        $employees = Employee::where('status', 'active')->get();
        $workingDays = $this->getWorkingDays($startDate, $endDate);

        $employeeSummary = [];

        foreach ($employees as $employee) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $halfDay = $attendances->where('status', 'half_day')->count();
            $onLeave = $attendances->where('status', 'on_leave')->count();

            $totalHours = $attendances->where('work_hours', '>', 0)->sum('work_hours');
            $averageHours = $attendances->where('work_hours', '>', 0)->count() > 0
                ? $totalHours / $attendances->where('work_hours', '>', 0)->count()
                : 0;

            $attendanceRate = $workingDays > 0 ? ($present / $workingDays) * 100 : 0;

            $employeeSummary[] = [
                'employee_id' => $employee->employee_id,
                'name' => $employee->name,
                'department' => $employee->department,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'on_leave' => $onLeave,
                'total_hours' => $totalHours,
                'average_hours' => $averageHours,
                'attendance_rate' => $attendanceRate,
            ];
        }

        // Overall statistics
        $totalAttendances = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        $totalPresent = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')->count();
        $totalAbsent = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'absent')->count();
        $totalLate = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'late')->count();

        $overallRate = $totalAttendances > 0 ? ($totalPresent / $totalAttendances) * 100 : 0;

        return [
            'employee_summary' => $employeeSummary,
            'working_days' => $workingDays,
            'total_employees' => $employees->count(),
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_late' => $totalLate,
            'overall_attendance_rate' => $overallRate,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    private function getWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Exclude weekends (Saturday = 6, Sunday = 0)
            if (!in_array($current->dayOfWeek, [0, 6])) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    public function exportToPdf()
    {
        $data = $this->getAttendanceData();

        $pdf = Pdf::loadView('pdf.attendance-report', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'attendance-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportToExcel()
    {
        return Excel::download(new AttendancesExport(), 'attendance-report-' . date('Y-m-d') . '.xlsx');
    }
}
