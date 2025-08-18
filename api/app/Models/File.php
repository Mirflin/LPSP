<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'path',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'file_lists', 'file_id', 'product_id');
    }
}
