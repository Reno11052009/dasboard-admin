<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@example.com',
        ]);

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
