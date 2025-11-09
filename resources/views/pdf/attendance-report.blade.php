<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .summary-stats {
            display: table;
            width: 100%;
        }
        .stat {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-stats">
            <div class="stat">
                <div class="stat-value">{{ number_format($total_employees) }}</div>
                <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($working_days) }}</div>
                <div class="stat-label">Working Days</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($total_present) }}</div>
                <div class="stat-label">Present Count</div>
            </div>
            <div class="stat">
                <div class="stat-value">{{ number_format($overall_attendance_rate, 1) }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>

    <h2 style="font-size: 14px; margin-top: 30px;">Employee Attendance Summary</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Late</th>
                <th class="text-center">Leave</th>
                <th class="text-right">Total Hrs</th>
                <th class="text-right">Avg Hrs</th>
                <th class="text-center">Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employee_summary as $summary)
            <tr>
                <td>{{ $summary['employee_id'] }}</td>
                <td>{{ $summary['name'] }}</td>
                <td>{{ $summary['department'] }}</td>
                <td class="text-center">{{ $summary['present'] }}</td>
                <td class="text-center">{{ $summary['absent'] }}</td>
                <td class="text-center">{{ $summary['late'] }}</td>
                <td class="text-center">{{ $summary['on_leave'] }}</td>
                <td class="text-right">{{ number_format($summary['total_hours'], 1) }}</td>
                <td class="text-right">{{ number_format($summary['average_hours'], 1) }}</td>
                <td class="text-center">
                    @if($summary['attendance_rate'] >= 95)
                        <span class="badge badge-success">{{ number_format($summary['attendance_rate'], 1) }}%</span>
                    @elseif($summary['attendance_rate'] >= 80)
                        <span class="badge badge-warning">{{ number_format($summary['attendance_rate'], 1) }}%</span>
                    @else
                        <span class="badge badge-danger">{{ number_format($summary['attendance_rate'], 1) }}%</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No employees found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
        <h3 style="margin: 0 0 10px 0; font-size: 12px;">Summary Statistics</h3>
        <p style="margin: 5px 0;"><strong>Total Present:</strong> {{ number_format($total_present) }}</p>
        <p style="margin: 5px 0;"><strong>Total Absent:</strong> {{ number_format($total_absent) }}</p>
        <p style="margin: 5px 0;"><strong>Total Late:</strong> {{ number_format($total_late) }}</p>
        <p style="margin: 5px 0;"><strong>Overall Attendance Rate:</strong> {{ number_format($overall_attendance_rate, 2) }}%</p>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html>
