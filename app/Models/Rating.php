<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Rating dimiliki oleh User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Rating dimiliki oleh Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}