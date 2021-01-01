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
        'urlImage',
        'points',
        'category_id'
    ];

    // Enrollments / Courses - Users / Many to Many + Defining Extra attribute completedDate in the pivot table
    public function users()
    {

        return $this->belongsToMany(User::class)->withPivot('completedDate')->withTimestamps();
    }

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
