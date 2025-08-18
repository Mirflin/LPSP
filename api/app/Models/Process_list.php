<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process_list extends Model
{
    public function processes()
    {
        return $this->hasMany(Process::class);
    }
}
