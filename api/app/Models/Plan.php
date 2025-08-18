<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'po_date',
        'po_nr',
        'user_id',
        'client_id',
        'product_id',
        'produced',
        'sended',
        'price',
        'total',
        'statuss',
        'extra_process',
        'invoice',
        'outsource_statuss',
    ];

    protected $appends = [
        'statuss_label',
        'outsource_statuss_label',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatussLabelAttribute()
    {
        $map = [
            0 => 'Pending',
            1 => 'In Production',
            2 => 'Completed',
            3 => 'Cancelled',
        ];

        return $map[$this->statuss] ?? 'Unknown';
    }

    public function getOutsourceStatussLabelAttribute()
    {
        $map = [
            0 => 'Not Outsourced',
            1 => 'Waiting Supplier',
            2 => 'In Outsourcing',
            3 => 'Delivered',
        ];

        return $map[$this->outsource_statuss] ?? 'Unknown';
    }

}
