<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'uid',
        'firstName',
        'lastName',
        'email',
        'pointsTarget',
        'isAdmin',
        'department_id'
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // Completion / Lectures - Users / Many to Many
    public function lectures()
    {
        return $this->belongsToMany(Lecture::class)->withTimestamps();
    }

    // Enrollments / Courses - Users / Many to Many + Defining Extra attribute completedDate in the pivot table
    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('completedDate')->withTimestamps();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function company()
    {
        return $this->hasOneThrough(Company::class, Department::class);
    }
}
