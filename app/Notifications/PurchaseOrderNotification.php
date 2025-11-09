<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $purchaseOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->purchaseOrder->load(['items.product', 'supplier']);

        return (new MailMessage)
            ->subject('New Purchase Order - ' . $this->purchaseOrder->po_number)
            ->markdown('emails.purchase-order', [
                'purchaseOrder' => $this->purchaseOrder,
                'supplier' => $this->purchaseOrder->supplier,
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
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
            'total_amount' => $this->purchaseOrder->total_amount,
            'expected_delivery_date' => $this->purchaseOrder->expected_delivery_date,
        ];
    }
}
