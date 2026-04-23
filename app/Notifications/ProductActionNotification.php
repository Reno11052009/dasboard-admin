<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductActionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $action,
        public string $productName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->action === 'create' 
            ? 'Product baru telah dibuat' 
            : 'Product telah diperbarui';

        return (new MailMessage)
            ->subject($message)
            ->line($message)
            ->line('Nama Product: ' . $this->productName)
            ->line('Aksi: ' . ucfirst($this->action))
            ->line('Waktu: ' . now()->format('Y-m-d H:i:s'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->action === 'create' ? 'Product Dibuat' : 'Product Diperbarui',
            'message' => 'Product "' . $this->productName . '" telah ' . ($this->action === 'create' ? 'dibuat' : 'diperbarui'),
            'action' => $this->action,
            'product' => $this->productName,
            'time' => now()->toDateTimeString(),
        ];
    }
}