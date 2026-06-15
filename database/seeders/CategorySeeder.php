<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu agar tidak duplikat saat running ulang (opsional)
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('categories')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            // Kategori Umum & Mesin
            ['id' => 1, 'judulKategori' => 'Suku Cadang Mesin'],
            ['id' => 2, 'judulKategori' => 'Sistem Pengereman'],
            ['id' => 3, 'judulKategori' => 'Kelistrikan & Busi'],
            ['id' => 4, 'judulKategori' => 'Transmisi & Kopling'],
            ['id' => 5, 'judulKategori' => 'Suspensi & Kaki-kaki'],
            
            // Perawatan & Cairan
            ['id' => 6, 'judulKategori' => 'Oli & Pelumas'],
            ['id' => 7, 'judulKategori' => 'Cairan Pembersih & Coolant'],
            ['id' => 8, 'judulKategori' => 'Filter (Oli, Udara, AC)'],
            
            // Eksterior & Interior
            ['id' => 9, 'judulKategori' => 'Body Part & Eksterior'],
            ['id' => 10, 'judulKategori' => 'Lampu & Penerangan'],
            ['id' => 11, 'judulKategori' => 'Ban & Velg'],
            ['id' => 12, 'judulKategori' => 'Aksesoris Interior'],
            
            // Tools & Gadget
            ['id' => 13, 'judulKategori' => 'Perkakas & Tools'],
            ['id' => 14, 'judulKategori' => 'Audio & Elektronik'],
            ['id' => 15, 'judulKategori' => 'Helm & Safety Gear'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['id' => $category['id']],
                [
                    'judulKategori' => $category['judulKategori'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
