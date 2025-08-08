<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_children extends Model
{
    protected $fillable = [
        'product_id',
        'children_product_id',
    ];
}
