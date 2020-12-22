<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, Category::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Department::class);
    }
}
