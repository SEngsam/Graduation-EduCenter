<?php

namespace App\Models;

use App\Support\Notifier;
use App\Scopes\UserAddedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\CourseNotification;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{

    use HasFactory,SoftDeletes;
     protected $guarded = [];
    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'registrations', 'course_id', 'student_id');
    }

    public function units()
    {
        return $this->hasMany(CourseUnit::class)->orderBy('position')->orderBy('id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position')->orderBy('id');
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class)->orderByDesc('starts_at');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('due_at');
    }

    public function slots()
    {
        return $this->hasMany(CourseSlot::class)->orderBy('day')->orderBy('starts_at');
    }

    public function notifyStudents($type, $params, $url)
    {
        $users = [];
        foreach ($this->students()->with('user')->get() as $student) {
            if ($student->user) {
                $users[] = $student->user;
            }
        }

        Notifier::send($users, new CourseNotification($type, $params, $this->title, $url));
    }

    public function isTaughtBy($user)
    {
        return $user && ($user->hasRole(Role::ROLE_ADMIN) || ($user->teacher && $this->teacher_id === $user->teacher->id));
    }
   
    public function createdBy(){
        return $this->belongsTo(User::class, 'user_id');
    }

   
    public function scopeCreatedBy($query){
        if (auth()->check()) {
            $user = Auth::user();
            if ($user->hasRole(Role::ROLE_TEACHER)) {
                $query->where('created_by',$user->id);
            }
        }

        
    }

    public function photo()
    {
        if ($this->image && file_exists(public_path('uploads/courses/' . $this->image))) {
            return asset('uploads/courses/' . $this->image);
        }

        return asset('frontend/images/courses/course-' . ($this->id % 6 + 1) . '.jpg');
    }
}
