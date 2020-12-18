<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    // Allocations / Courses - Departments / Many to Many
    public function courses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
