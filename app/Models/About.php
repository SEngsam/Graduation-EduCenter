<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class About extends Model
{
    use HasFactory,SoftDeletes;
     protected $guarded = [];

    public function photo()
    {
        if ($this->image && file_exists(public_path('uploads/abouts/' . $this->image))) {
            return asset('uploads/abouts/' . $this->image);
        }

        return asset('frontend/images/about/about-us.jpg');
    }
}
