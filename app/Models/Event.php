<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory,SoftDeletes;
     protected $guarded = [];

    public function photo()
    {
        if ($this->image && file_exists(public_path('uploads/events/' . $this->image))) {
            return asset('uploads/events/' . $this->image);
        }

        return asset('frontend/images/events/event-' . ($this->id % 3 + 1) . '.jpg');
    }
}
