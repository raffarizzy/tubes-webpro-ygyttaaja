<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Toko;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'toko_id'     => Toko::factory(),
            'category_id' => Category::factory(),
            'nama'        => fake()->words(3, true),
            'deskripsi'   => fake()->sentence(),
            'harga'       => fake()->numberBetween(10000, 500000),
            'diskon'      => 0,
            'stok'        => fake()->numberBetween(1, 100),
            'imagePath'   => 'produk/default.jpg',
        ];
    }
}
