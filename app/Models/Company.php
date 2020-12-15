<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasManyThrough('App\Models\User', 'App\Models\Department');
    }

    public function departments()
    {
        return $this->hasMany('App\Models\Department');
    }
}
