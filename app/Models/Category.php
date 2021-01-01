<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'colorVal', 'company_id'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
