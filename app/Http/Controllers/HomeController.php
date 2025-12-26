<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage with products
     */
    public function index()
    {
        // Get all products with relationships
        $products = Product::with(['category', 'toko'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all categories for potential filtering
        $categories = Category::all();

        return view('homepage', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}