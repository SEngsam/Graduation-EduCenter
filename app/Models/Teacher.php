<?php

namespace App\Models;

use App\Scopes\UserAddedScope;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];




    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function course()
    {
        return $this->hasMany(Course::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id')->withDefault();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearchByCategory($query, ?string $category_name = null)
    {
        return $query->when($category_name, function ($query) use ($category_name) {
            return $query->whereHas('category', function ($q) use ($category_name) {
                $q->where('name_major', 'like', '%' . $category_name . '%');
            });
        });
    }

    public function photo()
    {
        if ($this->image && file_exists(public_path('uploads/teachers/' . $this->image))) {
            return asset('uploads/teachers/' . $this->image);
        }

        return asset('frontend/images/teachers/teacher-' . ($this->id % 3 + 1) . '.jpg');
    }
}
