<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

$products = Product::all();
echo "Total products: " . $products->count() . "\n";
foreach ($products as $p) {
    echo "ID: {$p->id}, Name: {$p->nama}, ImagePath: {$p->imagePath}\n";
    if ($p->imagePath) {
        $exists = Storage::disk('public')->exists($p->imagePath);
        echo "  - File exists in public disk: " . ($exists ? "YES" : "NO") . "\n";
        echo "  - Storage URL: " . Storage::url($p->imagePath) . "\n";
    }
}
