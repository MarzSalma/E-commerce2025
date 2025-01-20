<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Fields that are mass-assignable
    protected $fillable = ['name', 'description', 'price', 'stock_quantity', 'image', 'category_id', 'shop_id', 'status'];

    // Relation: A product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relation: A product belongs to a shop
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
