<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'address',
        'email',
        'phone',
        'status',
        'seller_id',
    ];

   public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
{
    return $this->hasMany(Product::class);
}

}
