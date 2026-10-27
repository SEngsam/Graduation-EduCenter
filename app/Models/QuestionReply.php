<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionReply extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(CourseQuestion::class, 'course_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
