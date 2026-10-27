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

    public function overlaps($day, $start, $end)
    {
        return $this->day == $day
            && substr($this->starts_at, 0, 5) < substr($end, 0, 5)
            && substr($this->ends_at, 0, 5) > substr($start, 0, 5);
    }

    public function minutes($field = 'starts_at')
    {
        [$h, $m] = explode(':', $this->$field);

        return $h * 60 + $m;
    }

    public function time($field = 'starts_at')
    {
        return date('g:i', strtotime($this->$field));
    }
}
