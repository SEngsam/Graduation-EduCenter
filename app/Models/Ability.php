<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ability extends Model
{
    use HasFactory,SoftDeletes;
     protected $guarded = [];
     protected $table = 'abilities';

     public function roles()
    {
        return $this->belongsToMany(Role::class , 'ability_role','ability_id','role_id');
    }
}
