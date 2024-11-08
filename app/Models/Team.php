<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
   use HasFactory;
    Protected $primaryKey ='team_id';
    protected $table = 'teams';
    protected $fillable = [
        'hod_id',
        'employee_id',
        'profile_picture',
        'full_name',
        'id_number',
        'nationality',
        'email',
        'gender',
        'team',
    ];

    // Define the relationship with the HOD model (belongs to one HOD)
    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id');
    }

    // Define the relationship with the Employee model (belongs to one Employee)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
