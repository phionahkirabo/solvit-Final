<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'gender',
        'employment_status',
        'marital_status',
        'start_date',
        'qualification_level',
        'date_of_birth'
    ];
}
