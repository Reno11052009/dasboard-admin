<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Product;
use App\Models\User;

class ProductApprovalRequest extends Notification
{
    use Queueable;

    public $product;
    public $user;

    public function __construct(Product $product, User $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'product_approval',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'user_name' => $this->user->name,
            'message' => "{$this->user->name} meminta persetujuan produk baru: {$this->product->name}",
            'url' => route('products')
        ];
    }
}
