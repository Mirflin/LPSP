<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_material extends Model
{
    protected $fillable = [
        'product_id',
        'material_id',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
