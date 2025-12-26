<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'judulKategori' => 'Sparepart Mesin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'judulKategori' => 'Sparepart Body',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}