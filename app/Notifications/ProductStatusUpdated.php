<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class ProductStatusUpdated extends Notification
{
    use Queueable;

    public $product;
    public $status;

    public function __construct(Product $product, $status)
    {
        $this->product = $product;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $statusText = $this->status == 'approved' ? 'disetujui' : 'ditolak';
        return [
            'type' => 'product_status_updated',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'message' => "Pengajuan produk ({$this->product->name}) telah {$statusText}.",
            'url' => route('products')
        ];
    }
}
