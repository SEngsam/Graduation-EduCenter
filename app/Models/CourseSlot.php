<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSlot extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function dayName()
    {
        return __('teach.days')[$this->day];
    }

    public function time($field = 'starts_at')
    {
        return date('g:i', strtotime($this->$field));
    }
}
