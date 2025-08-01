<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
    ];

    public function productLists()
    {
        return $this->hasMany(ProductList::class);
    }
}
