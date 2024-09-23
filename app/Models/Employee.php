<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable implements JWTSubject
{
   use HasFactory, Notifiable,HasApiTokens;
   
     protected $fillable = [
        'employee_name',
        'email',
        'contact_number',
        'position',
        'hod_fk_id',
        'personalemail',
        'default_password'
        
    ];
/**
* The attributes that should be hidden for arrays.
*
* @var array
*/
protected $hidden = [
 'password',
 'default_password'
];
/**
* Get the identifier that will be stored in the subject claim of the JWT.
*
* @return mixed
*/
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

  public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_fk_id');
    }

}
