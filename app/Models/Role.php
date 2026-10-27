<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    const ROLE_ADMIN = 1;
    const ROLE_TEACHER = 4;
    const ROLE_STUDENT = 5;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'ability_role', 'role_id', 'ability_id');
    }
}
