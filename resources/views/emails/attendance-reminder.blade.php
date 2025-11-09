@component('mail::message')
# Attendance Reminder

Hello {{ $employee->name ?? $employee->email }},

@if($reminderType === 'check_in')
This is a friendly reminder to check in for work today.
@else
This is a friendly reminder to check out before leaving the office.
@endif

{{ $message }}

@component('mail::button', ['url' => config('app.url') . '/admin/attendances'])
Go to Attendance
@endcomponent

Have a great {{ $reminderType === 'check_in' ? 'day' : 'evening' }}!

Best regards,<br>
{{ config('app.name') }}
@endcomponent
