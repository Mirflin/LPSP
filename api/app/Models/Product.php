<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'drawing_nr',
        'part_nr',
        'revision',
        'description',
        'additional_info',
        'client_id',
        'weight'
    ];

    public function material_list()
    {
        return $this->belongsTo(Product_material::class);
    }
}
