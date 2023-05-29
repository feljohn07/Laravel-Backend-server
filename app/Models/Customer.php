<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'address'];

    public function order()
    {
        return $this->hasMany(Order::class, 'id');
    }

    use HasFactory;
}
