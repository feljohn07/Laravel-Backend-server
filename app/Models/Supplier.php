<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'address'];

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'id');
    }

    use HasFactory;
}
