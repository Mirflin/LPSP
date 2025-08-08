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
}
