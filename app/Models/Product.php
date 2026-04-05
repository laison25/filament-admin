<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = '23810310088_products';
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock_quantity', 'image_path',
        'status', 'discount_percent'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFinalPriceAttribute(): int
    {
        return (int) ($this->price * (1 - $this->discount_percent / 100));
    }
}