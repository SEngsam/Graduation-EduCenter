<?php

namespace App\Models;

use App\Scopes\UserAddedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'registrations', 'student_id', 'course_id');
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class)->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
