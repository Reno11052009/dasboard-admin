<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class produkseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Product',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'stok' => 100,
            'price' => 9.99,
            'image' => null,
            'category' => 'Category 1',
        ]);

        Product::create([
            'name' => 'Product 2',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'stok' => 50,
            'price' => 19.99,
            'image' => null,
            'category' => 'Category 2',
        ]);
    }
}
