<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'points',
        'category_id'
    ];

    public function lectures()
    {
        return $this->hasMany('App\Models\Lecture');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function company()
    {
        return $this->hasOneThrough(Company::class, Category::class);
    }
}
