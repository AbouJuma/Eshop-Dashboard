<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'discount',
        'description',
        'pin',
        'image',
        'product_category_id'
    ];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function productRating()
    {
        return $this->hasOne(ProductRating::class);
    }

    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }
}
