<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Hod extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Implement the methods required by JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'hod_name', 'email', 'contact_number', 'password','verification_code',
    ];

    protected $hidden = [
        'password',
    ];
    
    // Define the relationship to employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'hod_fk_id');
    }


    // HOD can have many tasks (if needed)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // HOD can have many comments (if needed)
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

}
