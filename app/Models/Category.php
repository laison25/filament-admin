<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = '23810310088_categories';
    protected $fillable = ['name', 'slug', 'description', 'is_visible'];
    protected $casts = ['is_visible' => 'boolean'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}