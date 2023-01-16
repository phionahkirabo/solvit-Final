<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    use HasFactory;
    protected $fillable = [
        'province',
        'district', 
        'sector', 
        'cell', 
        'village', 
        'streat_adress',
    ];
}
