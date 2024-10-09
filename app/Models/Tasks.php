<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'task_name',
        'description',
        'start_date',
        'due_date',
        'status',
        'project_id',
        'employee_id',
    ];

    // Relationship to Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Relationship to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
