<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Need users and products first. Run UserSeeder and ProductSeeder first.');
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $months = range(0, 5);

        foreach ($months as $monthOffset) {
            $orderCount = rand(3, 8);

            for ($i = 0; $i < $orderCount; $i++) {
                $user = $users->random();
                $product = $products->random();
                $quantity = rand(100, 500);
                $status = $statuses[array_rand($statuses)];
                $orderDate = Carbon::now()->subMonths($monthOffset)->subDays(rand(0, 28));

                Order::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_price' => $product->price * $quantity,
                    'status' => $status,
                    'order_date' => $orderDate,
                ]);
            }
        }
    }
}
