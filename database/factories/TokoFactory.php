<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TokoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'nama_toko'     => fake()->company(),
            'deskripsi_toko'=> fake()->sentence(),
            'lokasi'        => fake()->city(),
            'logo_path'     => null,
        ];
    }
}
