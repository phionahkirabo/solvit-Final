<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_name',
        'description',
        'start_date',
        'end_date',
        'status',
        'hod_id',
        'project_category',
    ];

    public function hod()
    {
        return $this->belongsTo(Hod::class, 'hod_id');
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class, 'project_id');
    }
}
