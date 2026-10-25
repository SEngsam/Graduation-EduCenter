<?php

namespace App\Models;

use App\Scopes\UserAddedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}

