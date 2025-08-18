<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_children extends Model
{
    protected $fillable = [
        'product_id',
        'children_product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function childProduct()
    {
        return $this->belongsTo(Product::class, 'children_product_id');
    }
}
