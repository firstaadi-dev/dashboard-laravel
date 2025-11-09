<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reminderType;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @param string $reminderType 'check_in' or 'check_out'
     */
    public function __construct(string $reminderType = 'check_in', string $message = null)
    {
        $this->reminderType = $reminderType;
        $this->message = $message ?? ($reminderType === 'check_in'
            ? 'Don\'t forget to check in for today!'
            : 'Remember to check out before leaving!');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->reminderType === 'check_in'
            ? 'Attendance Check-in Reminder'
            : 'Attendance Check-out Reminder';

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.attendance-reminder', [
                'employee' => $notifiable,
                'reminderType' => $this->reminderType,
                'message' => $this->message,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reminder_type' => $this->reminderType,
            'message' => $this->message,
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
