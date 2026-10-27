<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseQuestion extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function replies()
    {
        return $this->hasMany(QuestionReply::class)->oldest();
    }

    public function answeredByTeacher()
    {
        foreach ($this->replies as $reply) {
            if ($this->course->isTaughtBy($reply->user)) {
                return true;
            }
        }

        return false;
    }
}
