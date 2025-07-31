<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact_person',
        'bank',
        'iban',
        'phone',
        'delivery_address',
        'registration_nr',
        'pvn_nr',
        'email',
        'payment_term',
    ];
}
