<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status', 'shop_id'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

