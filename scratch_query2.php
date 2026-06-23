<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = \App\Models\Product::with('category')->get();
foreach ($products as $p) {
    echo "Product: " . $p->nama . " | Category: " . ($p->category ? $p->category->judulKategori : 'None') . "\n";
}








