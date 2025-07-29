<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lpsp_credentials extends Model
{
    protected $fillable = [
        'name',
        'address',
        'vat_nr',
        'bank',
        'iban',
        'export_address',
        'phone',
        'registration_nr',
    ];
}
