<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubPlan extends Model
{

    protected $fillable = [
        'plan_id',
        'product_id',
        'produced',
        'statuss',
        'cost',
        'outsource_statuss',
    ];

    protected $appends = [
        'statuss_label',
        'outsource_statuss_label',
    ];

    public function getStatussLabelAttribute()
    {
        $map = [
            0 => 'Pending',
            1 => 'In Production',
            2 => 'Completed',
            3 => 'Cancelled',
            4 => 'Aproved'
        ];

        return $map[$this->statuss] ?? 'Unknown';
    }

    public function getOutsourceStatussLabelAttribute()
    {
        $map = [
            0 => 'Not Outsourced',
            1 => 'Waiting Supplier',
            2 => 'Delivered',
            3 => 'Outsource Cancelled'
        ];

        return $map[$this->outsource_statuss] ?? 'Unknown';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
