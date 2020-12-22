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


    // Allocations / Courses - Departments / Many to Many
    public function departments()
    {
        return $this->belongsToMany(Department::class)->withTimestamps();
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->hasOneThrough(Company::class, Category::class);
    }
}
