<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_settings' => 'array',
    ];

    const EMAIL_TYPES = [
        'grade' => true,
        'assignment' => true,
        'reminder' => true,
        'absent' => true,
        'lesson' => false,
        'submission' => false,
    ];


    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }
    public function hasRole($roleId)
    {
        return $this->role->id == $roleId;
    }
    public function hasAbility($code)
    {
        if ($this->role_id == Role::ROLE_ADMIN) {
            return true;
        }

        return $this->role && $this->role->abilities->contains('code', $code);
    }
    public function wantsEmail($type)
    {
        $settings = $this->email_settings ?? [];

        if (array_key_exists($type, $settings)) {
            return (bool) $settings[$type];
        }

        return self::EMAIL_TYPES[$type] ?? false;
    }

    public function studentProfile()
    {
        if ($this->student) {
            return $this->student;
        }

        return Student::create([
            'name' => $this->name,
            'major' => '',
            'no_phone' => '',
            'user_id' => $this->id,
        ]);
    }
}
