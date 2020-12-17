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

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
