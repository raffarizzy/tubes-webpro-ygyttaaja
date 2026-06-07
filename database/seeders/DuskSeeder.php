<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Toko;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DuskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'tester@sparehub.com'],
            [
                'name' => 'Tester User',
                'phone' => '08123456789',
                'password' => Hash::make('password123'),
                'pfpPath' => 'https://i.ibb.co.com/ZRkqGfJ3/default-avatar-sparehubtize.png',
            ]
        );

        $toko = Toko::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_toko' => 'Mock Toko',
                'deskripsi_toko' => 'Toko Mock',
                'lokasi' => 'Bandung',
                'logo_path' => 'toko/mock.png'
            ]
        );

        $cat = Category::updateOrCreate(
            ['judulKategori' => 'Sparepart'],
            []
        );

        Product::updateOrCreate(
            ['id' => 1],
            [
                'toko_id' => $toko->id,
                'category_id' => $cat->id,
                'nama' => 'Busi Racing',
                'harga' => 50000,
                'stok' => 10,
                'deskripsi' => 'Busi kencang',
                'imagePath' => 'produk/busi.jpg'
            ]
        );
        
        Product::updateOrCreate(
            ['id' => 2],
            [
                'toko_id' => $toko->id,
                'category_id' => $cat->id,
                'nama' => 'Oli Mesin',
                'harga' => 80000,
                'stok' => 5,
                'deskripsi' => 'Oli licin',
                'imagePath' => 'produk/oli.jpg'
            ]
        );
    }
}
