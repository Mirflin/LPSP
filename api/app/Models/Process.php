<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = [
        'place',
        'sub_process',
        'product_id',
        'process_id',
        'price',
        'additional_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function processList()
    {
        return $this->belongsTo(ProcessList::class, 'process_id');
    }
}
