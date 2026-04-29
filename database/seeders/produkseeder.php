<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class produkseeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop ASUS VivoBook', 'description' => 'Laptop dengan processor Intel Core i5', 'stok' => 25, 'price' => 5500000, 'category' => 'Electronics'],
            ['name' => 'Keyboard Mechanical', 'description' => 'Mechanical keyboard dengan RGB lighting', 'stok' => 50, 'price' => 450000, 'category' => 'Electronics'],
            ['name' => 'Mouse Wireless', 'description' => 'Mouse wireless dengan precision sensor', 'stok' => 80, 'price' => 150000, 'category' => 'Electronics'],
            ['name' => 'Monitor LG 24 Inch', 'description' => 'Monitor LED Full HD 24 inch', 'stok' => 15, 'price' => 1800000, 'category' => 'Electronics'],
            ['name' => 'Headphone Sony', 'description' => 'Headphone noise cancelling', 'stok' => 30, 'price' => 750000, 'category' => 'Electronics'],
            ['name' => 'Webcam HD', 'description' => 'Webcam 1080p untuk video call', 'stok' => 40, 'price' => 350000, 'category' => 'Electronics'],
            ['name' => 'Flashdisk 32GB', 'description' => 'Flashdisk USB 3.0 32GB', 'stok' => 200, 'price' => 50000, 'category' => 'Storage'],
            ['name' => 'Harddisk Eksternal 1TB', 'description' => 'Harddisk eksternal 1TB USB 3.0', 'stok' => 20, 'price' => 650000, 'category' => 'Storage'],
            ['name' => 'SSD 256GB', 'description' => 'SSD internal 256GB SATA III', 'stok' => 35, 'price' => 400000, 'category' => 'Storage'],
            ['name' => 'MicroSD 64GB', 'description' => 'MicroSD 64GB dengan adapter', 'stok' => 150, 'price' => 75000, 'category' => 'Storage'],
            ['name' => 'Kabel HDMI 2Meter', 'description' => 'Kabel HDMI 2 meter', 'stok' => 100, 'price' => 65000, 'category' => 'Accessories'],
            ['name' => 'Charger Laptop', 'description' => 'Charger laptop universal 65W', 'stok' => 45, 'price' => 250000, 'category' => 'Accessories'],
            ['name' => 'Powerbank 10000mAh', 'description' => 'Powerbank 10000mAh fast charging', 'stok' => 60, 'price' => 280000, 'category' => 'Accessories'],
            ['name' => 'USB Hub 4 Port', 'description' => 'USB Hub 4 port USB 3.0', 'stok' => 55, 'price' => 120000, 'category' => 'Accessories'],
            ['name' => 'Laptop Sleeve', 'description' => 'Sleeve case untuk laptop 14 inch', 'stok' => 70, 'price' => 95000, 'category' => 'Accessories'],
            ['name' => 'Gaming Chair', 'description' => 'Kursi gaming ergonomis', 'stok' => 10, 'price' => 2500000, 'category' => 'Furniture'],
            ['name' => 'Meja Komputer', 'description' => 'Meja komputer dengan drawer', 'stok' => 12, 'price' => 850000, 'category' => 'Furniture'],
            ['name' => 'Soundbar Speaker', 'description' => 'Soundbar untuk komputer', 'stok' => 25, 'price' => 550000, 'category' => 'Electronics'],
            ['name' => 'Microphone USB', 'description' => 'Microphone USB untuk recording', 'stok' => 30, 'price' => 320000, 'category' => 'Electronics'],
            ['name' => 'Webcam 4K', 'description' => 'Webcam 4K dengan autofocus', 'stok' => 15, 'price' => 850000, 'category' => 'Electronics'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
