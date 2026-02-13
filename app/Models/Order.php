<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'amount',
        'reference_no',
        'user_id',
        'address_id'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function eshops(){
        return $this->hasMany(OrderEshop::class);
    }
}
