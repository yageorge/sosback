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

    // Including attribute isCompleted => getIsCompletedAttribute in all lectures requests
    protected $appends = ['completedDate'];

    // Eloquent accessor / adding new timestamp (in api responses) attribute checking if Course is compelted by current user
    public function getCompletedDateAttribute()
    {
        // Find pivot record between $this course and current user:
        $courseUserEnrollment = $this->users()->where('user_id', current_user()->id)->get()->first();

        // if $user not null => current user is enrolled to this course:
        if ($courseUserEnrollment !== null) {
            // if not null, return completedDate that can be itself null (not completed) or date (course is completed by user)
            return $courseUserEnrollment->pivot->completedDate;
        }
        return null;
    }

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
