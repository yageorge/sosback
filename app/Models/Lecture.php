<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'urlVideo',
        'duration',
        'course_id'
    ];

    // Including attribute isCompleted => getIsCompletedAttribute in all lectures requests
    protected $appends = ['isCompleted'];

    // Eloquent accessor / adding new boolean (in api responses) attribute checking if Lecture is compelted by current user
    public function getIsCompletedAttribute()
    {
        return $this->users()->where('user_id', current_user()->id)->exists();
    }

    // Completions / Lecture - Users / Many to Many
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
