<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Report Period
            </x-slot>

            <form wire:submit="generate">
                {{ $this->form }}
            </form>
        </x-filament::section>

        @php
            $attendanceData = $this->getAttendanceData();
        @endphp

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::section>
                <x-slot name="heading">
                    Total Employees
                </x-slot>
                <div class="text-3xl font-bold text-primary-600">
                    {{ number_format($attendanceData['total_employees']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Working Days
                </x-slot>
                <div class="text-3xl font-bold text-info-600">
                    {{ number_format($attendanceData['working_days']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Present Count
                </x-slot>
                <div class="text-3xl font-bold text-success-600">
                    {{ number_format($attendanceData['total_present']) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Overall Attendance Rate
                </x-slot>
                <div class="text-3xl font-bold text-primary-600">
                    {{ number_format($attendanceData['overall_attendance_rate'], 1) }}%
                </div>
            </x-filament::section>
        </div>

        {{-- Status Breakdown --}}
        <x-filament::section>
            <x-slot name="heading">
                Attendance Status Breakdown
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Absent</div>
                        <div class="text-2xl font-bold text-red-600">{{ number_format($attendanceData['total_absent']) }}</div>
                    </div>
                    <x-filament::icon
                        icon="heroicon-o-x-circle"
                        class="w-10 h-10 text-red-600"
                    />
                </div>

                <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Late</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($attendanceData['total_late']) }}</div>
                    </div>
                    <x-filament::icon
                        icon="heroicon-o-clock"
                        class="w-10 h-10 text-yellow-600"
                    />
                </div>

                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">On Time</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ number_format($attendanceData['total_present'] - $attendanceData['total_late']) }}
                        </div>
                    </div>
                    <x-filament::icon
                        icon="heroicon-o-check-circle"
                        class="w-10 h-10 text-green-600"
                    />
                </div>
            </div>
        </x-filament::section>

        {{-- Employee Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                Employee Attendance Summary
            </x-slot>
            <x-slot name="description">
                Period: {{ Carbon\Carbon::parse($attendanceData['start_date'])->format('d M Y') }} - {{ Carbon\Carbon::parse($attendanceData['end_date'])->format('d M Y') }}
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Present</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Absent</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Late</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">On Leave</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hours</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Hours/Day</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($attendanceData['employee_summary'] as $summary)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $summary['employee_id'] }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $summary['name'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $summary['department'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                    {{ $summary['present'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                    {{ $summary['absent'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                    {{ $summary['late'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                    {{ $summary['on_leave'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($summary['total_hours'], 1) }}h
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($summary['average_hours'], 1) }}h
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($summary['attendance_rate'] >= 95) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($summary['attendance_rate'] >= 80) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                        @endif">
                                        {{ number_format($summary['attendance_rate'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                    No employees found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
