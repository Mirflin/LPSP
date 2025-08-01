<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function material_list()
    {
        return $this->belongsTo(Product_material::class);
    }
}
