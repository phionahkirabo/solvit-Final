<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
     protected $fillable = [
        'task_name',      // Task name
        'description',    // Task description
        'start_date',     // Start date
        'due_date',       // Due date
        'status',         // Task status
        'project_id',     // Foreign key referencing projects table
        'employee_id'     // Foreign key referencing employees table
    ];
}
