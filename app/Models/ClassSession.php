<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $guarded = [];

    protected $casts = ['starts_at' => 'datetime'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function rate()
    {
        $total = $this->attendances->count();

        if (! $total) {
            return null;
        }

        $came = $this->attendances->whereIn('status', ['present', 'late'])->count();

        return (int) round($came * 100 / $total);
    }
}
