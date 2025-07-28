<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersProfiles extends Model
{
    protected $fillable = [
        'phone',
        'bio',
        'country',
        'city',
        'postal_code',
        'facebook',
        'twitter',
        'instagram',
        'linkedId',
    ];
}
