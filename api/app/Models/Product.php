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
        'weight',
        'count'
    ];

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'product_materials', 'product_id', 'material_id');
    }

    public function children()
    {
        return $this->belongsToMany(Product::class, 'product_childrens', 'product_id', 'children_product_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'file_lists', 'product_id', 'file_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function childrenWithRelations($depth = 1)
    {
        if ($depth <= 0) return $this->children();

        return $this->children()
                    ->with([
                        'client',
                        'processes.processList',
                        'materials',
                        'files',
                        'children' => function($q) use ($depth) {
                            $q->with(['client','processes.processList','materials','files'])
                            ->with(['children' => fn($q2) => $q2->childrenWithRelations($depth - 1)]);
                        }
                    ]);
    }
}
