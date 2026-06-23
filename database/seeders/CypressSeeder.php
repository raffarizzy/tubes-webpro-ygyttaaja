<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeder khusus E2E Cypress (idempotent).
 *
 * Menyiapkan kondisi awal yang dibutuhkan oleh test PIC Naufal:
 *  - User login   : tester@medcom.com   / Password123!
 *  - User duplikat : existing@medcom.com / Password123!  (untuk uji email sudah dipakai)
 *  - Produk id=1   : stok = 10           (batas BVA TC-BB-N02)
 *
 * Aman dijalankan berulang: password & nama tester selalu di-reset ke baseline,
 * sehingga test yang mengubah profil/password tidak merusak run berikutnya.
 *
 * Jalankan: php artisan db:seed --class=CypressSeeder
 */
class CypressSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users -------------------------------------------------------
        User::updateOrCreate(
            ['email' => 'tester@medcom.com'],
            [
                'name'              => 'Naufal Tester',
                'password'          => 'Password123!', // di-hash otomatis (cast 'hashed')
                'email_verified_at' => Carbon::now(),
                'phone'             => '081200000001',
                'birthDate'         => '2000-01-01',
                'gender'            => 'male',
                'pfpPath'           => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'existing@medcom.com'],
            [
                'name'              => 'Existing User',
                'password'          => 'Password123!',
                'email_verified_at' => Carbon::now(),
                'phone'             => '081200000002',
                'birthDate'         => '2000-01-01',
                'gender'            => 'female',
                'pfpPath'           => null,
            ]
        );

        // --- Produk id=1 dengan stok = 10 --------------------------------
        $product = Product::withTrashed()->find(1);

        if ($product) {
            $product->forceFill([
                'stok'       => 10,
                'deleted_at' => null, // pastikan tidak ter-soft-delete
            ])->save();
        } else {
            // DB kosong: siapkan kategori & toko minimal agar FK valid
            $category = Category::query()->first()
                ?? Category::create(['nama' => 'Oli']);

            $toko = Toko::query()->first()
                ?? Toko::create(['nama_toko' => 'Medcom Official']);

            Product::create([
                'id'          => 1,
                'toko_id'     => $toko->id,
                'category_id' => $category->id,
                'nama'        => 'Oli Mesin 10W-40',
                'deskripsi'   => 'Oli mesin untuk motor harian',
                'harga'       => 75000,
                'diskon'      => 0,
                'stok'        => 10,
                'imagePath'   => 'produk/default.png',
            ]);
        }
    }
}
