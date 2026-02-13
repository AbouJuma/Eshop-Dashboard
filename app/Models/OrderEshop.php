<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderEshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'p_name',
        'p_image',
        'p_price',
        'p_quantity',
        'p_sku'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
