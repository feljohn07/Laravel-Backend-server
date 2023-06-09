<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    // product_name
    // minimum_quantity
    // retail_price
    // quantity_on_hand

    protected $fillable = ['product_name', 'minimum_quantity', 'retail_price', 'quantity_on_hand'];

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'id');
    }

    use HasFactory;
}
